# 🎯 AI Recommendation System - Guide Complet

## ✅ Solution Finale : Serveur HTTP Persistent

**Problème résolu** : Windows Python 3.12 asyncio bug (OSError WinError 10106)  
**Solution** : Serveur HTTP qui garde le modèle en mémoire (beaucoup plus rapide)

---

## 🚀 Démarrage Rapide

### 1. Démarrer le serveur AI (obligatoire)

```powershell
# Option 1 : Via artisan (recommandé)
php artisan ai:start-server

# Option 2 : Directement avec Python
.\.venv\Scripts\python.exe ai_server.py
```

**Le serveur prend ~10 secondes à démarrer** (chargement du modèle sentence-transformers).  
Une fois démarré, il affiche :

```
🚀 AI Recommendation Server running on http://127.0.0.1:8765
```

### 2. Démarrer Laravel

```powershell
php artisan serve
```

### 3. Utiliser l'IA

-   Aller sur : http://127.0.0.1:8000/participations/create
-   Cliquer : **"Suggérer avec l'IA"**
-   L'IA sélectionne le meilleur greenspace en ~2-3 secondes

---

## 📊 Performance

| Moteur                | Temps de réponse | Qualité                                           |
| --------------------- | ---------------- | ------------------------------------------------- |
| **embeddings_only**   | 2-3s             | ⭐⭐⭐⭐ Très bon                                 |
| **ollama+embeddings** | 20-25s           | ⭐⭐⭐⭐⭐ Excellent (explications intelligentes) |

---

## 🧪 Tests

### Vérifier que le serveur AI fonctionne

```powershell
# Test santé serveur
Invoke-WebRequest -Uri 'http://127.0.0.1:8765/health'

# Test complet via Laravel
php artisan test:fast-ai
```

**Résultat attendu** :

```
✅ Success! (2000-3000ms)
Best Match ID | 3
Score         | 0.56
Engine        | embeddings_only
```

### Test depuis le navigateur

1. Connecte-toi : http://127.0.0.1:8000/login
2. Va sur : http://127.0.0.1:8000/participations/create
3. Clique : "Suggérer avec l'IA"
4. L'IA retourne le meilleur greenspace avec explication

---

## 🛠️ Architecture

### Fichiers Clés

```
urbanGreen/
├── ai_server.py                      # 🔥 Serveur AI HTTP (à lancer en premier)
├── app/Services/FastAIRecommender.php # Service Laravel qui appelle le serveur
├── app/Http/Controllers/
│   └── ParticipationController.php   # Controller avec route suggest()
└── app/Console/Commands/
    ├── StartAIServer.php             # Commande: php artisan ai:start-server
    └── TestFastAI.php                # Commande: php artisan test:fast-ai
```

### Flow de Données

```
[User clicks "Suggérer"]
    ↓
[ParticipationController::suggest()]
    ↓
[FastAIRecommender::recommend()]  ← HTTP call
    ↓
[ai_server.py:8765]
    ├─ Charge sentence-transformers (déjà en mémoire)
    ├─ Calcule similarité sémantique
    ├─ Tente Ollama (si disponible)
    └─ Retourne: best_match_id, score, reason
    ↓
[JSON response to browser]
```

---

## 🎨 Technologies Utilisées

-   **sentence-transformers** : `paraphrase-multilingual-MiniLM-L12-v2`
-   **Embeddings** : Similarité cosinus (numpy)
-   **Ollama** : llama3.1 (optionnel, pour explications avancées)
-   **Serveur** : Python HTTPServer
-   **Laravel** : Http client avec timeout 35s

---

## 🔧 Configuration

### Variables d'environnement (.env)

```env
AI_SERVER_URL=http://127.0.0.1:8765
```

### Port du serveur AI

Défini dans `ai_server.py` ligne 14 :

```python
PORT = 8765  # Change si conflit
```

---

## ⚡ Optimisations

### Avec Ollama (explications intelligentes)

1. **Démarre Ollama** :

```powershell
Start-Process -FilePath 'C:\Users\moeta\AppData\Local\Programs\Ollama\ollama.exe' -ArgumentList 'serve' -WindowStyle Hidden
```

2. **Vérifie qu'il fonctionne** :

```powershell
Invoke-RestMethod -Uri 'http://127.0.0.1:11434/api/generate' -Method Post -Body '{"model":"llama3.1","prompt":"Test","stream":false}' -ContentType 'application/json'
```

3. **Restart le serveur AI** pour qu'il utilise Ollama

**Temps de réponse** : ~20-25s (embeddings 2-3s + Ollama 18-22s)

### Sans Ollama (plus rapide)

Garde seulement les embeddings. L'IA dit :

> "Jardin Botanique correspond le mieux à vos préférences (57% de similarité)"

**Temps de réponse** : ~2-3s

---

## 🐛 Troubleshooting

### Erreur : "AI server is not available"

**Solution** :

```powershell
php artisan ai:start-server
```

### Erreur : "OSError [WinError 10106]"

**Cause** : Tu utilises l'ancien `AIRecommender` (subprocess)  
**Solution** : Le controller utilise maintenant `FastAIRecommender` (HTTP) ✅

### Serveur AI lent au démarrage

**Normal** : Le modèle sentence-transformers prend ~10s à charger la première fois.  
Une fois chargé, les requêtes sont rapides (2-3s).

### Timeout après 35 secondes

**Cause** : Ollama prend trop de temps  
**Solution** : Soit augmente timeout dans `FastAIRecommender.php` ligne 56 :

```php
$response = Http::timeout(60)  // était 35
```

Soit désactive Ollama pour utiliser seulement embeddings (2-3s).

---

## 📝 Commandes Utiles

```powershell
# Démarrer serveur AI
php artisan ai:start-server

# Tester l'IA
php artisan test:fast-ai

# Voir les logs Laravel
Get-Content storage/logs/laravel.log -Tail 50

# Vérifier santé serveur AI
Invoke-WebRequest http://127.0.0.1:8765/health

# Arrêter tous les processus Python
Get-Process python | Stop-Process -Force
```

---

## 🎉 Résumé

✅ **sentence-transformers** installé et fonctionnel  
✅ **Embeddings avancés** (multilingual, 384 dimensions)  
✅ **Serveur HTTP persistent** (pas de subprocess lent)  
✅ **Windows asyncio bug** contourné  
✅ **Temps de réponse** : 2-3s (embeddings) ou 20-25s (avec Ollama)  
✅ **Fallback intelligent** si Ollama indisponible

**Tu es prêt ! 🚀**
