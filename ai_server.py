"""
Fast recommendation server with Smart Scoring (embeddings + features)
No need for Ollama - generates intelligent explanations using feature analysis
Much faster and more precise than generic LLM!
"""

import asyncio
import sys
import os

# Windows asyncio fix
if sys.platform == 'win32':
    asyncio.set_event_loop_policy(asyncio.WindowsSelectorEventLoopPolicy())
    os.environ['TQDM_DISABLE'] = '1'

import json
import time
from http.server import HTTPServer, BaseHTTPRequestHandler
from sentence_transformers import SentenceTransformer
import numpy as np
from math import radians, cos, sin, sqrt, atan2

# Configuration
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


def haversine_distance(lat1, lon1, lat2, lon2):
    """Calculate distance in km between two GPS coordinates"""
    R = 6371  # Earth radius in km
    
    lat1, lon1, lat2, lon2 = map(radians, [lat1, lon1, lat2, lon2])
    dlat = lat2 - lat1
    dlon = lon2 - lon1
    
    a = sin(dlat/2)**2 + cos(lat1) * cos(lat2) * sin(dlon/2)**2
    c = 2 * atan2(sqrt(a), sqrt(1-a))
    
    return R * c


def calculate_features(user_prefs, greenspace, embedding_similarity):
    """
    Calculate all features for a greenspace
    Returns dict with all computed features
    """
    features = {}
    
    # ═══════════════════════════════════════════════════════════
    # 1. EMBEDDING SIMILARITY (already computed)
    # ═══════════════════════════════════════════════════════════
    features['embedding_similarity'] = embedding_similarity
    
    # ═══════════════════════════════════════════════════════════
    # 2. ACTIVITY MATCH (Jaccard Similarity)
    # ═══════════════════════════════════════════════════════════
    user_activities = set(user_prefs.get('preferred_activities', []))
    gs_activities = set(greenspace.get('activities', []))
    
    if user_activities and gs_activities:
        intersection = len(user_activities & gs_activities)
        union = len(user_activities | gs_activities)
        features['activity_match'] = intersection / union if union > 0 else 0
        features['matched_activities'] = list(user_activities & gs_activities)
    else:
        features['activity_match'] = 0
        features['matched_activities'] = []
    
    # ═══════════════════════════════════════════════════════════
    # 3. EXPERIENCE LEVEL MATCH
    # ═══════════════════════════════════════════════════════════
    experience_mapping = {
        "débutant": 1,
        "intermédiaire": 2,
        "expert": 3
    }
    
    user_level = experience_mapping.get(
        user_prefs.get('experience_level', 'débutant').lower(), 
        1
    )
    gs_level = experience_mapping.get(
        greenspace.get('complexity_level', 'débutant').lower(), 
        1
    )
    
    # Score: 1.0 if perfect match, decreases if too complex/simple
    level_diff = abs(user_level - gs_level)
    features['experience_match'] = max(0, 1 - (level_diff * 0.3))
    
    # ═══════════════════════════════════════════════════════════
    # 4. GEOGRAPHIC DISTANCE
    # ═══════════════════════════════════════════════════════════
    user_coords = user_prefs.get('coordinates')
    gs_coords = greenspace.get('coordinates')
    
    if user_coords and gs_coords:
        distance_km = haversine_distance(
            user_coords.get('lat', 0), user_coords.get('lon', 0),
            gs_coords.get('lat', 0), gs_coords.get('lon', 0)
        )
        max_distance = user_prefs.get('max_distance', 20)
        
        # Decreasing score with distance
        features['distance_score'] = max(0, 1 - (distance_km / max_distance))
        features['distance_km'] = round(distance_km, 1)
    else:
        features['distance_score'] = 0.5  # Neutral if no data
        features['distance_km'] = None
    
    # ═══════════════════════════════════════════════════════════
    # 5. SPACE TYPE MATCH
    # ═══════════════════════════════════════════════════════════
    preferred_types = user_prefs.get('preferred_types', [])
    gs_type = greenspace.get('type', '')
    
    features['type_match'] = 1.0 if gs_type in preferred_types else 0.3
    
    # ═══════════════════════════════════════════════════════════
    # 6. SPECIAL INTERESTS MATCH
    # ═══════════════════════════════════════════════════════════
    user_interests = set(user_prefs.get('interests', []))
    gs_description = greenspace.get('description', '').lower()
    
    matched_interests = [
        interest for interest in user_interests 
        if interest.lower() in gs_description
    ]
    
    features['interest_match'] = len(matched_interests) / max(len(user_interests), 1)
    features['matched_interests'] = matched_interests
    
    return features


def calculate_final_score(features):
    """
    Combine all features with optimized weights
    Returns final score between 0 and 1
    
    PRIORITÉS:
    1. Activités matchées (ce que l'utilisateur veut faire)
    2. Similarité sémantique (description pertinente)
    3. Intérêts spéciaux dans la description
    4. Distance (proximité)
    5. Type d'espace
    6. Niveau d'expérience (bonus mineur)
    """
    # Weights for each feature (total = 1.0)
    WEIGHTS = {
        'activity_match':       0.40,   # 40% - PRIORITÉ #1: Activités matchées
        'embedding_similarity': 0.30,   # 30% - Similarité sémantique (description)
        'distance_score':       0.15,   # 15% - Proximité géographique
        'type_match':           0.10,   # 10% - Type d'espace préféré
        'experience_match':     0.05,   # 5%  - Niveau (bonus mineur seulement)
    }
    
    # Calculate weighted score
    final_score = 0.0
    for feature, weight in WEIGHTS.items():
        final_score += features.get(feature, 0) * weight
    
    # Bonus important si intérêts spéciaux matchent dans la description (+10%)
    interest_bonus = features.get('interest_match', 0) * 0.10
    final_score = min(1.0, final_score + interest_bonus)
    
    return final_score


def generate_personalized_explanation(user_prefs, greenspace, features, score):
    """
    Generate natural explanation based on dominant features
    Returns human-readable French explanation
    
    PRIORITÉS:
    1. Activités matchées (le plus important)
    2. Intérêts dans la description
    3. Distance
    4. Type d'espace
    5. Niveau (seulement si très pertinent)
    """
    gs_name = greenspace['name']
    location = greenspace.get('location', '')
    
    # ═══════════════════════════════════════════════════════════
    # Build user-friendly, professional explanation
    # ═══════════════════════════════════════════════════════════
    
    highlights = []
    
    # 1. MATCHED ACTIVITIES (Top Priority)
    if features['activity_match'] > 0.3 and features['matched_activities']:
        activities = features['matched_activities'][:2]  # Limit to 2 for simplicity
        if len(activities) == 1:
            highlights.append(f"✓ {activities[0].capitalize()}")
        else:
            highlights.append(f"✓ {activities[0].capitalize()} & {activities[1]}")
    
    # 2. DISTANCE (if close)
    if features.get('distance_km') is not None and features['distance_km'] < 8:
        if features['distance_km'] < 2:
            highlights.append(f"✓ Très proche ({features['distance_km']:.1f}km)")
        elif features['distance_km'] < 5:
            highlights.append(f"✓ À proximité ({features['distance_km']:.1f}km)")
        else:
            highlights.append(f"✓ Accessible ({features['distance_km']:.1f}km)")
    
    # 3. SPACE TYPE (if matches)
    if features['type_match'] == 1.0:
        gs_type = greenspace.get('type', '').lower()
        type_labels = {
            'jardin communautaire': 'Jardin communautaire',
            'parc urbain': 'Parc urbain',
            'forêt': 'Espace naturel',
            'autre': 'Espace vert'
        }
        type_label = type_labels.get(gs_type, gs_type.capitalize())
        highlights.append(f"✓ {type_label}")
    
    # ═══════════════════════════════════════════════════════════
    # Format final explanation
    # ═══════════════════════════════════════════════════════════
    if highlights:
        # Professional, bullet-point style
        highlights_text = ' • '.join(highlights)
        if location:
            return f"{highlights_text} • {location}"
        else:
            return highlights_text
    else:
        # Simple fallback
        match_percentage = int(score * 100)
        if location:
            return f"{match_percentage}% compatible • {location}"
        else:
            return f"{match_percentage}% compatible avec vos préférences"


def get_recommendation(user_data: dict, green_spaces: list) -> dict:
    """
    Smart Scoring Recommendation (No Ollama needed!)
    Combines embeddings + feature engineering + intelligent explanations
    """
    start_time = time.time()
    
    if not green_spaces:
        return {
            "best_match_id": None,
            "score": 0.0,
            "reason": "Aucun espace vert disponible",
            "engine": "smart_scoring",
            "computation_time": "0ms",
            "top_rankings": []
        }
    
    # Get user preferences
    user_prefs = user_data.get('preferences', {})
    
    # ════════════════════════════════════════════════════════════
    # STEP 1: Build user profile text for embeddings
    # ════════════════════════════════════════════════════════════
    interests = ', '.join(user_prefs.get('interests', []))
    activities = ', '.join(user_prefs.get('preferred_activities', []))
    experience = user_prefs.get('experience_level', '')
    
    user_text = f"Intérêts: {interests}. Activités préférées: {activities}. Niveau: {experience}"
    
    # ════════════════════════════════════════════════════════════
    # STEP 2: Compute embedding similarities (pre-filter)
    # ════════════════════════════════════════════════════════════
    candidates = []
    
    for gs in green_spaces:
        gs_activities = ', '.join(gs.get('activities', []))
        gs_text = f"{gs['name']}. {gs.get('description', '')}. Activités: {gs_activities}"
        
        similarity = compute_similarity(user_text, gs_text)
        
        candidates.append({
            **gs,
            '_similarity_score': similarity
        })
    
    # Keep top 5 by embeddings for detailed analysis
    candidates = sorted(
        candidates, 
        key=lambda x: x['_similarity_score'], 
        reverse=True
    )[:5]
    
    # ════════════════════════════════════════════════════════════
    # STEP 3: Feature Engineering + Final Scoring
    # ════════════════════════════════════════════════════════════
    enriched_candidates = []
    
    for gs in candidates:
        # Calculate all features
        features = calculate_features(
            user_prefs,
            gs,
            gs['_similarity_score']
        )
        
        # Calculate weighted final score
        final_score = calculate_final_score(features)
        
        enriched_candidates.append({
            'greenspace': gs,
            'features': features,
            'final_score': final_score
        })
    
    # Sort by final score
    enriched_candidates.sort(key=lambda x: x['final_score'], reverse=True)
    best_candidate = enriched_candidates[0]
    
    # ════════════════════════════════════════════════════════════
    # STEP 4: Generate personalized explanation
    # ════════════════════════════════════════════════════════════
    explanation = generate_personalized_explanation(
        user_prefs,
        best_candidate['greenspace'],
        best_candidate['features'],
        best_candidate['final_score']
    )
    
    computation_time = (time.time() - start_time) * 1000
    
    return {
        "best_match_id": best_candidate['greenspace']['id'],
        "score": best_candidate['final_score'],
        "reason": explanation,
        "engine": "smart_scoring_v1",
        "computation_time": f"{computation_time:.2f}ms",
        "top_rankings": [
            {
                "id": c['greenspace']['id'],
                "name": c['greenspace']['name'],
                "final_score": round(c['final_score'], 3),
                "embedding_score": round(c['greenspace']['_similarity_score'], 3)
            }
            for c in enriched_candidates[:3]
        ]
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
