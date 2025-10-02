"""
AI Recommender for GreenSpace Participation using RAG (Retrieval-Augmented Generation)
Uses embeddings for fast pre-filtering + Ollama for intelligent reasoning
"""

# CRITICAL FIX for Windows Python 3.12 asyncio bug
# This MUST be done BEFORE importing any module that uses asyncio
import sys
import os

# Set these BEFORE any imports
os.environ['TQDM_DISABLE'] = '1'
os.environ['PYTHONIOENCODING'] = 'utf-8'

# Windows-specific fix: set selector event loop
if sys.platform == 'win32':
    # This must happen before asyncio is imported anywhere
    import asyncio.windows_events
    asyncio.set_event_loop_policy(asyncio.windows_events.WindowsSelectorEventLoopPolicy())

# Now safe to import other modules
import json
import requests
from typing import List, Dict, Any, Optional

# Import WITHOUT progress bars to avoid asyncio
import warnings
warnings.filterwarnings('ignore')

from sentence_transformers import SentenceTransformer
import numpy as np
from pathlib import Path

# Configuration
OLLAMA_BASE_URL = "http://localhost:11434"
OLLAMA_MODEL = "llama3.1"
EMBEDDING_MODEL = "paraphrase-multilingual-MiniLM-L12-v2"  # Fast & multilingual


class GreenSpaceRecommender:
    """RAG-based recommender for matching users with green spaces"""
    
    def __init__(self):
        """Initialize the recommender with embedding model"""
        try:
            self.embedding_model = SentenceTransformer(EMBEDDING_MODEL)
        except Exception as e:
            print(json.dumps({
                "error": f"Failed to load embedding model: {str(e)}",
                "engine": "embedding_model"
            }), file=sys.stderr)
            sys.exit(1)
    
    def create_user_profile_text(self, user_prefs: Dict) -> str:
        """Convert user preferences JSON to readable text"""
        if not user_prefs:
            return "Utilisateur sans préférences spécifiques"
        
        parts = []
        if interests := user_prefs.get('interests'):
            parts.append(f"Intéressé par: {', '.join(interests)}")
        if activities := user_prefs.get('preferred_activities'):
            parts.append(f"Préfère les activités: {', '.join(activities)}")
        if availability := user_prefs.get('availability'):
            parts.append(f"Disponibilité: {availability}")
        if level := user_prefs.get('experience_level'):
            parts.append(f"Niveau d'expérience: {level}")
        
        return " | ".join(parts) if parts else "Utilisateur ouvert à toutes les activités"
    
    def create_greenspace_text(self, gs: Dict) -> str:
        """Convert green space data to readable text for embedding"""
        parts = [
            gs.get('name', ''),
            gs.get('description', ''),
        ]
        
        # Add activities
        if activities := gs.get('activities'):
            if isinstance(activities, list):
                parts.append(f"Activités: {', '.join(activities)}")
            elif isinstance(activities, str):
                parts.append(f"Activités: {activities}")
        
        return " ".join(filter(None, parts))
    
    def retrieve_top_candidates(
        self, 
        user_prefs: Dict, 
        greenspaces: List[Dict],
        top_k: int = 2
    ) -> List[Dict]:
        """
        STEP 1 (RAG): Use embeddings to quickly retrieve most relevant green spaces
        This drastically reduces the amount of data sent to Ollama
        """
        if not greenspaces:
            return []
        
        # Create text representations
        user_text = self.create_user_profile_text(user_prefs)
        greenspace_texts = [self.create_greenspace_text(gs) for gs in greenspaces]
        
        # Generate embeddings
        user_embedding = self.embedding_model.encode(user_text, convert_to_tensor=False)
        gs_embeddings = self.embedding_model.encode(greenspace_texts, convert_to_tensor=False)
        
        # Calculate cosine similarities
        user_norm = user_embedding / np.linalg.norm(user_embedding)
        gs_norms = gs_embeddings / np.linalg.norm(gs_embeddings, axis=1, keepdims=True)
        similarities = np.dot(gs_norms, user_norm)
        
        # Get top K candidates
        top_indices = np.argsort(similarities)[::-1][:top_k]
        
        # Return top candidates with similarity scores
        candidates = []
        for idx in top_indices:
            candidate = greenspaces[idx].copy()
            candidate['_similarity_score'] = float(similarities[idx])
            candidates.append(candidate)
        
        return candidates
    
    def generate_recommendation_with_ollama(
        self, 
        user_prefs: Dict,
        candidates: List[Dict]
    ) -> Dict[str, Any]:
        """
        STEP 2 (RAG): Use Ollama to intelligently choose THE BEST match and explain why
        Input is pre-filtered, so Ollama response is FAST (<10 seconds)
        """
        if not candidates:
            return {
                "best_match_id": None,
                "score": 0.0,
                "reason": "Aucun espace vert disponible",
                "engine": "ollama"
            }
        
        # Build a concise prompt for Ollama
        user_profile = self.create_user_profile_text(user_prefs)
        
        candidates_info = []
        for i, gs in enumerate(candidates, 1):
            activities = gs.get('activities', [])
            if isinstance(activities, list):
                activities_str = ', '.join(activities)
            else:
                activities_str = str(activities)
            
            candidates_info.append(
                f"{i}. ID={gs['id']}, Nom='{gs['name']}', "
                f"Activités=[{activities_str}], "
                f"Score de similarité={gs.get('_similarity_score', 0):.2f}"
            )
        
        prompt = f"""Tu es un expert en recommandation d'espaces verts urbains.

PROFIL DE L'UTILISATEUR:
{user_profile}

ESPACES VERTS CANDIDATS (pré-filtrés):
{chr(10).join(candidates_info)}

TÂCHE:
Choisis LE MEILLEUR espace vert pour cet utilisateur et explique POURQUOI en 2-3 phrases maximum.

RÉPONDS EXACTEMENT dans ce format JSON (sans markdown):
{{
  "best_match_id": <id_du_meilleur_choix>,
  "reason": "Explication courte et claire du pourquoi"
}}"""
        
        # Call Ollama API (using /api/generate endpoint)
        try:
            response = requests.post(
                f"{OLLAMA_BASE_URL}/api/generate",
                json={
                    "model": OLLAMA_MODEL,
                    "prompt": prompt,
                    "stream": False,
                    "options": {
                        "temperature": 0.3,  # Low temperature for consistent results
                        "top_p": 0.9,
                        "num_predict": 100,  # Limit response to keep it fast
                    }
                },
                timeout=30  # 30 seconds max for Ollama
            )
            response.raise_for_status()
            
            ollama_result = response.json()
            response_text = ollama_result.get('response', '').strip()
            
            # Parse JSON from response
            # Remove markdown code blocks if present
            if '```json' in response_text:
                response_text = response_text.split('```json')[1].split('```')[0].strip()
            elif '```' in response_text:
                response_text = response_text.split('```')[1].split('```')[0].strip()
            
            # Extract JSON
            result = json.loads(response_text)
            
            # Validate and enrich
            best_id = result.get('best_match_id')
            reason = result.get('reason', 'Correspondance basée sur vos préférences')
            
            # Find the matching candidate to get its similarity score
            best_candidate = next((c for c in candidates if c['id'] == best_id), candidates[0])
            
            return {
                "best_match_id": best_id if best_id else best_candidate['id'],
                "score": best_candidate.get('_similarity_score', 0.85),
                "reason": reason,
                "engine": "ollama+embeddings"
            }
            
        except requests.Timeout:
            # Fallback to best similarity score if Ollama times out
            best = candidates[0]
            return {
                "best_match_id": best['id'],
                "score": best.get('_similarity_score', 0.8),
                "reason": "Sélectionné automatiquement car Ollama a pris trop de temps (meilleure similarité avec vos préférences)",
                "engine": "embeddings_fallback"
            }
        except requests.RequestException as e:
            # Ollama connection error - fallback to embeddings only
            best = candidates[0]
            return {
                "best_match_id": best['id'],
                "score": best.get('_similarity_score', 0.8),
                "reason": f"Sélectionné par similarité sémantique (Ollama indisponible: {str(e)[:50]})",
                "engine": "embeddings_only"
            }
        except (json.JSONDecodeError, KeyError) as e:
            # JSON parsing error - use first candidate
            best = candidates[0]
            return {
                "best_match_id": best['id'],
                "score": best.get('_similarity_score', 0.8),
                "reason": "Meilleure correspondance basée sur vos préférences et les activités disponibles",
                "engine": "embeddings_fallback_parse_error"
            }
    
    def recommend(self, user_data: Dict, greenspaces_data: List[Dict]) -> Dict[str, Any]:
        """
        Main recommendation pipeline (RAG approach)
        1. Retrieve top 2-3 candidates using embeddings (FAST)
        2. Generate final recommendation using Ollama (ACCURATE)
        """
        user_prefs = user_data.get('preferences', {})
        
        # Step 1: Fast retrieval with embeddings
        candidates = self.retrieve_top_candidates(user_prefs, greenspaces_data, top_k=2)
        
        if not candidates:
            return {
                "best_match_id": None,
                "score": 0.0,
                "reason": "Aucun espace vert disponible pour vos préférences",
                "engine": "none"
            }
        
        # Step 2: Intelligent selection with Ollama
        result = self.generate_recommendation_with_ollama(user_prefs, candidates)
        
        return result


def main():
    """CLI entry point"""
    if len(sys.argv) < 3:
        print(json.dumps({
            "error": "Usage: python ai_recommender.py <user_json> <greenspaces_json>"
        }), file=sys.stderr)
        sys.exit(1)
    
    try:
        # Parse input arguments
        user_data = json.loads(sys.argv[1])
        greenspaces_data = json.loads(sys.argv[2])
        
        # Initialize recommender
        recommender = GreenSpaceRecommender()
        
        # Get recommendation
        result = recommender.recommend(user_data, greenspaces_data)
        
        # Output JSON result with UTF-8 encoding
        # Use sys.stdout.buffer to write raw bytes with UTF-8 encoding
        output = json.dumps(result, ensure_ascii=False)
        
        # On Windows, force UTF-8 output
        if sys.platform == 'win32':
            sys.stdout.buffer.write(output.encode('utf-8'))
        else:
            print(output)
        
    except json.JSONDecodeError as e:
        print(json.dumps({
            "error": f"Invalid JSON input: {str(e)}"
        }), file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(json.dumps({
            "error": f"Unexpected error: {str(e)}"
        }), file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
