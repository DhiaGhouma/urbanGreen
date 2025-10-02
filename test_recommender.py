"""
Test script for AI recommender
"""
import sys
import json

# Add parent directory to path
sys.path.insert(0, '.')

from ai_recommender import GreenSpaceRecommender

# Test data
user_data = {
    "id": 1,
    "name": "Test User",
    "preferences": {
        "interests": ["jardinage", "nature"],
        "preferred_activities": ["plantation", "entretien"],
        "availability": "weekends",
        "experience_level": "débutant"
    }
}

greenspaces_data = [
    {
        "id": 1,
        "name": "Parc Central",
        "description": "Beau parc avec des arbres et des fleurs",
        "activities": ["plantation", "arrosage", "entretien"]
    },
    {
        "id": 2,
        "name": "Jardin Botanique",
        "description": "Jardin avec des espèces rares et des ateliers de jardinage",
        "activities": ["jardinage", "plantation", "ateliers"]
    },
    {
        "id": 3,
        "name": "Espace Vert Nord",
        "description": "Espace pour le sport et les événements communautaires",
        "activities": ["sport", "événements"]
    }
]

print("=" * 60)
print("TEST DU SYSTÈME DE RECOMMANDATION RAG")
print("=" * 60)
print()

print("🤖 Initialisation du recommender...")
recommender = GreenSpaceRecommender()
print("✅ Recommender initialisé avec succès")
print()

print("📊 DONNÉES DE TEST:")
print(f"   Utilisateur: {user_data['name']}")
print(f"   Intérêts: {', '.join(user_data['preferences']['interests'])}")
print(f"   Activités préférées: {', '.join(user_data['preferences']['preferred_activities'])}")
print(f"   Nombre d'espaces verts: {len(greenspaces_data)}")
print()

print("🔍 ÉTAPE 1: Retrieval (Embeddings)...")
candidates = recommender.retrieve_top_candidates(
    user_data['preferences'], 
    greenspaces_data, 
    top_k=2
)
print(f"   ✅ {len(candidates)} candidats sélectionnés:")
for i, c in enumerate(candidates, 1):
    print(f"      {i}. {c['name']} (score: {c['_similarity_score']:.3f})")
print()

print("🤖 ÉTAPE 2: Generation (Ollama)...")
print("   ⚠️  Ollama peut prendre 5-30 secondes...")
try:
    result = recommender.generate_recommendation_with_ollama(
        user_data['preferences'],
        candidates
    )
    print(f"   ✅ Recommandation générée avec succès")
    print()
    print("=" * 60)
    print("🎯 RÉSULTAT FINAL:")
    print("=" * 60)
    print(f"   Meilleur choix: ID #{result['best_match_id']}")
    print(f"   Score: {result['score']:.2f}")
    print(f"   Engine: {result['engine']}")
    print(f"   Raison: {result['reason']}")
    print("=" * 60)
except Exception as e:
    print(f"   ❌ Erreur: {e}")
    print()
    print("   Note: Si Ollama n'est pas lancé, le système utilisera le fallback automatiquement")
