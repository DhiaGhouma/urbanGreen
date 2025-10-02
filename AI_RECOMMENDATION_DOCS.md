# 🤖 Système de Recommandation AI - Documentation

## 📋 Vue d'ensemble

Ce système utilise une approche **RAG (Retrieval-Augmented Generation)** pour recommander le meilleur espace vert à un utilisateur lors de la création d'une participation.

### Architecture

```
┌─────────────┐
│   Laravel   │ ← Interface utilisateur
│ Controller  │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ AIRecommend │ ← Service PHP
│   Service   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Python    │ ← Script RAG
│ai_recommender│
└──────┬──────┘
       │
       ├─► 🔍 Embeddings (sentence-transformers)
       │   └─► Pré-filtrage rapide (< 2 secondes)
       │
       └─► 🤖 Ollama (llama3.1)
           └─► Sélection intelligente avec explication (< 10 secondes)
```

## 🚀 Fonctionnalités

### 1. **Gestion des Préférences Utilisateur**

-   Interface web pour définir ses préférences
-   Route: `/preferences`
-   Stockage JSON dans la table `users`

### 2. **Recommandation AI**

-   Bouton "Suggérer avec l'IA" dans le formulaire de participation
-   Appel AJAX vers `/participations/suggest/ai`
-   Affichage du résultat avec explication

### 3. **Fallback Automatique**

-   Si Ollama est indisponible → Utilise uniquement les embeddings
-   Si Python échoue → Utilise matching par mots-clés
-   **Zéro interruption de service**

## 📁 Fichiers Créés

### Backend PHP (Laravel)

```
app/
├── Services/
│   ├── OllamaService.php          → Interface avec Ollama API
│   └── AIRecommender.php          → Orchestration de la recommandation
└── Http/Controllers/
    └── UserPreferenceController.php → Gestion des préférences
```

### Backend Python

```
ai_recommender.py                   → Script RAG principal
test_recommender.py                 → Script de test
```

### Frontend (Blade)

```
resources/views/users/
└── preferences.blade.php           → Page de configuration des préférences
```

### Database

```
database/seeders/
├── UserPreferencesSeeder.php       → Ajoute des préférences par défaut
└── GreenSpaceActivitiesSeeder.php  → Ajoute des activités aux espaces verts
```

## 🔧 Configuration

### Variables d'environnement (.env)

```env
PYTHON_BIN='C:\Users\moeta\OneDrive\Desktop\5eme-Projects\urbanGreen\.venv\Scripts\python.exe'
OLLAMA_BIN='C:\Users\moeta\AppData\Local\Programs\Ollama\ollama.exe'
OLLAMA_MODEL=llama3.1
```

### Routes (routes/web.php)

```php
// Préférences utilisateur
Route::get('/preferences', [UserPreferenceController::class, 'edit'])->name('preferences.edit');
Route::put('/preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

// Recommandation AI
Route::get('participations/suggest/ai', [ParticipationController::class, 'suggest'])
    ->name('participations.suggest')
    ->middleware('auth');
```

## 🎯 Utilisation

### Pour l'utilisateur

1. **Configurer ses préférences**

    - Menu utilisateur → "Mes Préférences IA"
    - Sélectionner intérêts, activités, disponibilité, niveau d'expérience
    - Enregistrer

2. **Créer une participation**
    - Menu → Participations → Nouvelle Participation
    - Cliquer sur "Suggérer avec l'IA"
    - L'espace vert recommandé est automatiquement sélectionné
    - Une explication est affichée

### Pour le développeur

#### Tester le script Python

```bash
cd C:\Users\moeta\OneDrive\Desktop\5eme-Projects\urbanGreen
.\.venv\Scripts\python.exe test_recommender.py
```

#### Appeler le service depuis Laravel

```php
use App\Services\AIRecommender;

public function suggest(Request $request, AIRecommender $recommender)
{
    $user = auth()->user();
    $greenspaces = GreenSpace::all();

    $result = $recommender->recommend($user, $greenspaces);

    return response()->json($result);
}
```

## ⚡ Performance

### Temps de réponse typiques

| Scénario                    | Temps                                  |
| --------------------------- | -------------------------------------- |
| Embeddings seuls (fallback) | **1-3 secondes** ⚡                    |
| RAG complet (Ollama actif)  | **5-15 secondes** 🚀                   |
| Ollama première utilisation | 15-30 secondes (téléchargement modèle) |

### Optimisations appliquées

1. **Pré-filtrage avec embeddings** : Réduit drastiquement la quantité de données envoyées à Ollama
2. **Top-K = 2** : Seulement 2 candidats envoyés à Ollama au lieu de tous
3. **Temperature = 0.3** : Réponses plus consistantes et rapides
4. **Timeout adaptatif** : 45s pour Ollama, fallback automatique après
5. **Modèle d'embedding léger** : `paraphrase-multilingual-MiniLM-L12-v2` (471 MB)

## 🛠️ Maintenance

### Démarrer Ollama manuellement

```powershell
Start-Process -FilePath 'C:\Users\moeta\AppData\Local\Programs\Ollama\ollama.exe' -ArgumentList 'serve' -WindowStyle Hidden
```

### Vérifier les logs

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Vérifier si Ollama est actif
curl http://localhost:11434/api/tags
```

### Ajouter un nouveau modèle Ollama

```bash
ollama pull llama3.2
```

Puis modifier `.env` :

```env
OLLAMA_MODEL=llama3.2
```

## 🧪 Tests

### Test unitaire Python

```bash
.\.venv\Scripts\python.exe test_recommender.py
```

### Test d'intégration Laravel

```bash
php artisan test --filter=AIRecommenderTest
```

## 🐛 Dépannage

### Problème : "Ollama indisponible"

**Solution 1** : Démarrer Ollama

```powershell
& 'C:\Users\moeta\AppData\Local\Programs\Ollama\ollama.exe' serve
```

**Solution 2** : Le système utilise automatiquement le fallback (embeddings seuls)

### Problème : "Python script timeout"

**Cause** : Le modèle d'embedding se télécharge la première fois (471 MB)

**Solution** : Attendre la première exécution (9-10 minutes), puis c'est instantané

### Problème : "Recommandation toujours la même"

**Cause** : Préférences utilisateur vides ou identiques

**Solution** : Aller dans "Mes Préférences IA" et personnaliser

## 📊 Structure des Données

### User Preferences (JSON)

```json
{
    "interests": ["jardinage", "nature", "fleurs"],
    "preferred_activities": ["plantation", "entretien", "arrosage"],
    "availability": "weekends",
    "experience_level": "débutant"
}
```

### GreenSpace Activities (JSON)

```json
["plantation", "arrosage", "entretien", "nettoyage", "événements"]
```

### Résultat de Recommandation

```json
{
    "best_match_id": 2,
    "score": 0.85,
    "reason": "Le Jardin Botanique correspond parfaitement à vos intérêts en jardinage et nature. Les ateliers de plantation proposés sont idéaux pour votre niveau débutant.",
    "engine": "ollama+embeddings",
    "computation_time": "8.5s"
}
```

## 🔐 Sécurité

-   ✅ Authentification requise pour la recommandation
-   ✅ Validation des entrées utilisateur
-   ✅ Timeout sur les appels externes (Ollama)
-   ✅ Fallback automatique en cas d'erreur
-   ✅ Logs détaillés pour audit

## 📈 Améliorations Futures

1. **Cache Redis** : Mettre en cache les embeddings des greenspaces
2. **Historique** : Apprendre des choix précédents de l'utilisateur
3. **A/B Testing** : Comparer différentes stratégies de recommandation
4. **Multi-critères** : Ajouter distance géographique, disponibilité horaire
5. **API REST** : Exposer le service en API pour applications mobiles

## 📝 Crédits

-   **Modèle d'embedding** : sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2
-   **LLM** : Meta Llama 3.1 (via Ollama)
-   **Framework RAG** : Langchain
-   **Framework Web** : Laravel 11

---

**Développé avec ❤️ pour UrbanGreen**

_Version 1.0 - Octobre 2025_
