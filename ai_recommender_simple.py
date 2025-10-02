"""
Alternative AI Recommender WITHOUT sentence-transformers
Uses simple TF-IDF instead to avoid Windows asyncio issues
"""

import sys
import os
import json
import requests
from typing import List, Dict, Any
from collections import Counter
import math

# Simple text processing without external dependencies
class SimpleRecommender:
    """Simple TF-IDF based recommender without heavy dependencies"""
    
    def __init__(self):
        pass
    
    def tokenize(self, text: str) -> List[str]:
        """Simple tokenization"""
        return text.lower().split()
    
    def compute_tfidf_similarity(self, query_text: str, doc_texts: List[str]) -> List[float]:
        """Compute TF-IDF similarity scores"""
        # Tokenize
        query_tokens = self.tokenize(query_text)
        docs_tokens = [self.tokenize(doc) for doc in doc_texts]
        
        # Build vocabulary
        vocab = set(query_tokens)
        for doc_tokens in docs_tokens:
            vocab.update(doc_tokens)
        
        # Compute IDF
        idf = {}
        n_docs = len(docs_tokens)
        for term in vocab:
            df = sum(1 for doc in docs_tokens if term in doc)
            idf[term] = math.log((n_docs + 1) / (df + 1)) + 1
        
        # Compute TF-IDF vectors
        def get_tfidf_vector(tokens):
            tf = Counter(tokens)
            vector = {}
            for term in vocab:
                vector[term] = tf.get(term, 0) * idf.get(term, 0)
            return vector
        
        query_vector = get_tfidf_vector(query_tokens)
        doc_vectors = [get_tfidf_vector(doc) for doc in docs_tokens]
        
        # Compute cosine similarity
        def cosine_sim(v1, v2):
            dot_product = sum(v1.get(k, 0) * v2.get(k, 0) for k in vocab)
            norm1 = math.sqrt(sum(v ** 2 for v in v1.values()))
            norm2 = math.sqrt(sum(v ** 2 for v in v2.values()))
            if norm1 == 0 or norm2 == 0:
                return 0
            return dot_product / (norm1 * norm2)
        
        similarities = [cosine_sim(query_vector, doc_vec) for doc_vec in doc_vectors]
        return similarities
    
    def create_user_profile_text(self, user_prefs: Dict) -> str:
        """Convert user preferences to text"""
        parts = []
        if interests := user_prefs.get('interests', []):
            parts.extend(interests)
        if activities := user_prefs.get('preferred_activities', []):
            parts.extend(activities)
        if activities := user_prefs.get('activities_interest', []):
            parts.extend(activities)
        return " ".join(parts)
    
    def create_greenspace_text(self, gs: Dict) -> str:
        """Convert greenspace to text"""
        parts = [
            gs.get('name', ''),
            gs.get('description', ''),
        ]
        if activities := gs.get('activities'):
            if isinstance(activities, list):
                parts.extend(activities)
        return " ".join(filter(None, parts))
    
    def recommend(self, user_data: Dict, greenspaces_data: List[Dict]) -> Dict[str, Any]:
        """Simple recommendation without heavy ML libraries"""
        user_prefs = user_data.get('preferences', {})
        
        if not greenspaces_data:
            return {
                "best_match_id": None,
                "score": 0.0,
                "reason": "Aucun espace vert disponible",
                "engine": "simple_tfidf"
            }
        
        # Create text representations
        user_text = self.create_user_profile_text(user_prefs)
        
        if not user_text.strip():
            # No preferences - return first greenspace
            return {
                "best_match_id": greenspaces_data[0]['id'],
                "score": 0.5,
                "reason": "Espace vert sélectionné par défaut (aucune préférence spécifiée)",
                "engine": "simple_default"
            }
        
        greenspace_texts = [self.create_greenspace_text(gs) for gs in greenspaces_data]
        
        # Compute similarities
        similarities = self.compute_tfidf_similarity(user_text, greenspace_texts)
        
        # Find best match
        best_idx = similarities.index(max(similarities))
        best_gs = greenspaces_data[best_idx]
        best_score = similarities[best_idx]
        
        # If score is 0, use simple keyword matching as fallback
        if best_score == 0:
            # Count keyword matches
            user_keywords = set(user_text.lower().split())
            match_scores = []
            
            for gs in greenspaces_data:
                gs_text = self.create_greenspace_text(gs).lower()
                matches = sum(1 for keyword in user_keywords if keyword in gs_text)
                match_scores.append(matches)
            
            best_idx = match_scores.index(max(match_scores))
            best_gs = greenspaces_data[best_idx]
            best_score = match_scores[best_idx] / max(len(user_keywords), 1)
        
        # Generate reason
        if best_score > 0.3:
            reason = f"Excellent choix basé sur vos préférences : {best_gs.get('name', 'N/A')} correspond bien à vos intérêts"
        elif best_score > 0.1:
            reason = f"Bon choix : {best_gs.get('name', 'N/A')} propose des activités qui peuvent vous intéresser"
        else:
            reason = f"{best_gs.get('name', 'N/A')} - Espace vert recommandé pour découvrir de nouvelles activités"
        
        return {
            "best_match_id": best_gs['id'],
            "score": float(best_score),
            "reason": reason,
            "engine": "simple_tfidf_enhanced"
        }


def main():
    """CLI entry point"""
    if len(sys.argv) < 3:
        print(json.dumps({
            "error": "Usage: python ai_recommender_simple.py <user_json> <greenspaces_json>"
        }), file=sys.stderr)
        sys.exit(1)
    
    try:
        # Parse input
        user_data = json.loads(sys.argv[1])
        greenspaces_data = json.loads(sys.argv[2])
        
        # Create recommender
        recommender = SimpleRecommender()
        
        # Get recommendation
        result = recommender.recommend(user_data, greenspaces_data)
        
        # Output with UTF-8
        output = json.dumps(result, ensure_ascii=False)
        if sys.platform == 'win32':
            sys.stdout.buffer.write(output.encode('utf-8'))
        else:
            print(output)
        
    except json.JSONDecodeError as e:
        print(json.dumps({"error": f"Invalid JSON: {str(e)}"}), file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(json.dumps({"error": f"Error: {str(e)}"}), file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
