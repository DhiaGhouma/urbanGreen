"""
Fast recommendation server that keeps model loaded in memory
Run once: python ai_server.py
Then call from Laravel via HTTP (much faster than subprocess)
"""

import asyncio
import sys
import os

# Windows asyncio fix
if sys.platform == 'win32':
    asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())
    os.environ['TQDM_DISABLE'] = '1'

import json
from http.server import HTTPServer, BaseHTTPRequestHandler
from sentence_transformers import SentenceTransformer
import numpy as np
import requests

# Configuration
OLLAMA_BASE_URL = "http://127.0.0.1:11434"
OLLAMA_MODEL = "llama3.1"
PORT = 8765

# Load model once at startup
print("🔄 Loading sentence-transformer model...", file=sys.stderr)
model = SentenceTransformer('sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2')
print("✅ Model loaded!", file=sys.stderr)


def compute_similarity(text1: str, text2: str) -> float:
    """Compute cosine similarity between two texts"""
    emb1 = model.encode([text1])[0]
    emb2 = model.encode([text2])[0]
    
    dot_product = np.dot(emb1, emb2)
    norm1 = np.linalg.norm(emb1)
    norm2 = np.linalg.norm(emb2)
    
    if norm1 == 0 or norm2 == 0:
        return 0.0
    
    return float(dot_product / (norm1 * norm2))


def get_recommendation(user_data: dict, green_spaces: list) -> dict:
    """Main recommendation logic"""
    
    # Build user profile
    prefs = user_data.get('preferences', {})
    interests = ', '.join(prefs.get('interests', []))
    activities = ', '.join(prefs.get('preferred_activities', []))
    experience = prefs.get('experience_level', '')
    
    user_text = f"Intérêts: {interests}. Activités préférées: {activities}. Niveau: {experience}"
    
    # Compute similarities
    for gs in green_spaces:
        gs_activities = ', '.join(gs.get('activities', []))
        gs_text = f"{gs['name']}. {gs.get('description', '')}. Activités: {gs_activities}"
        
        similarity = compute_similarity(user_text, gs_text)
        gs['_similarity_score'] = similarity
    
    # Sort by similarity
    green_spaces.sort(key=lambda x: x.get('_similarity_score', 0), reverse=True)
    
    # Get top 2 candidates
    candidates = green_spaces[:2]
    
    if not candidates:
        return {
            "best_match_id": None,
            "score": 0.0,
            "reason": "Aucun espace vert disponible",
            "engine": "embeddings_only"
        }
    
    # Try Ollama for intelligent reasoning
    try:
        candidates_info = []
        for i, gs in enumerate(candidates, 1):
            activities_str = ', '.join(gs.get('activities', []))
            candidates_info.append(
                f"{i}. ID={gs['id']}, Nom='{gs['name']}', "
                f"Activités=[{activities_str}], "
                f"Score={gs['_similarity_score']:.2f}"
            )
        
        prompt = f"""Tu es un expert en recommandation d'espaces verts urbains.

PROFIL UTILISATEUR: {user_text}

CANDIDATS (pré-filtrés):
{chr(10).join(candidates_info)}

Choisis LE MEILLEUR et explique POURQUOI en 2 phrases max.

RÉPONDS en JSON (sans markdown):
{{
  "best_match_id": <id>,
  "reason": "Explication courte"
}}"""

        response = requests.post(
            f"{OLLAMA_BASE_URL}/api/generate",
            json={
                "model": OLLAMA_MODEL,
                "prompt": prompt,
                "stream": False,
                "options": {"temperature": 0.3, "num_predict": 80}
            },
            timeout=20
        )
        response.raise_for_status()
        
        ollama_result = response.json()
        response_text = ollama_result.get('response', '').strip()
        
        # Parse JSON
        if '```json' in response_text:
            response_text = response_text.split('```json')[1].split('```')[0].strip()
        elif '```' in response_text:
            response_text = response_text.split('```')[1].split('```')[0].strip()
        
        result = json.loads(response_text)
        best_id = result.get('best_match_id')
        reason = result.get('reason', 'Correspondance basée sur vos préférences')
        
        best_candidate = next((c for c in candidates if c['id'] == best_id), candidates[0])
        
        return {
            "best_match_id": best_id if best_id else best_candidate['id'],
            "score": best_candidate['_similarity_score'],
            "reason": reason,
            "engine": "ollama+embeddings"
        }
        
    except Exception as e:
        # Fallback to embeddings only
        best = candidates[0]
        return {
            "best_match_id": best['id'],
            "score": best['_similarity_score'],
            "reason": f"{best['name']} correspond le mieux à vos préférences ({best['_similarity_score']:.0%} de similarité)",
            "engine": "embeddings_only"
        }


class RecommendationHandler(BaseHTTPRequestHandler):
    def log_message(self, format, *args):
        # Suppress default logging
        pass
    
    def do_POST(self):
        if self.path != '/recommend':
            self.send_response(404)
            self.end_headers()
            return
        
        try:
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
            
            user_data = data.get('user')
            green_spaces = data.get('green_spaces', [])
            
            result = get_recommendation(user_data, green_spaces)
            
            self.send_response(200)
            self.send_header('Content-Type', 'application/json; charset=utf-8')
            self.end_headers()
            self.wfile.write(json.dumps(result, ensure_ascii=False).encode('utf-8'))
            
        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            error = {"error": str(e)}
            self.wfile.write(json.dumps(error).encode('utf-8'))
    
    def do_GET(self):
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-Type', 'text/plain')
            self.end_headers()
            self.wfile.write(b'OK')
        else:
            self.send_response(404)
            self.end_headers()


if __name__ == '__main__':
    server = HTTPServer(('127.0.0.1', PORT), RecommendationHandler)
    print(f"🚀 AI Recommendation Server running on http://127.0.0.1:{PORT}", file=sys.stderr)
    print(f"   - Health check: http://127.0.0.1:{PORT}/health", file=sys.stderr)
    print(f"   - Recommend: POST http://127.0.0.1:{PORT}/recommend", file=sys.stderr)
    
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n👋 Server stopped", file=sys.stderr)
