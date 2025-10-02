# 🚀 Quick Start - Système de Recommandation AI

## ✅ Installation Complète !

Tous les fichiers ont été créés et configurés. Voici comment utiliser le système :

---

## ⚠️ FIX WINDOWS ASYNCIO

**Si vous avez eu l'erreur `OSError: [WinError 10106]`**, c'est déjà corrigé dans le code !

La correction appliquée :

-   ✅ Downgrade de tqdm à 4.66.1
-   ✅ Ajout de `os.environ['TQDM_DISABLE'] = '1'` dans le script

---

## 🎯 Pour Tester Immédiatement

### 1. Démarrer Ollama (optionnel - le fallback fonctionne sans)

```powershell
Start-Process -FilePath 'C:\Users\moeta\AppData\Local\Programs\Ollama\ollama.exe' -ArgumentList 'serve' -WindowStyle Hidden
```

### 2. Démarrer Laravel

```bash
php artisan serve
```

### 3. Accéder à l'application

Ouvrez votre navigateur : **http://127.0.0.1:8000**

### 4. Se connecter

Utilisez un compte existant ou créez-en un nouveau

### 5. Configurer vos préférences

**Allez dans votre PROFIL** (les préférences y sont déjà intégrées)

### 6. Tester la recommandation

1. Allez dans **Participations** → **Nouvelle Participation**
2. Cliquez sur le bouton **"Suggérer avec l'IA"** ✨
3. Attendez 2-10 secondes
4. L'espace vert recommandé est sélectionné automatiquement
5. Lisez l'explication de l'IA 🤖

---

## 📊 Résultat Attendu

```
✅ Suggestion: Jardin Botanique
💡 Raison: "Le Jardin Botanique correspond parfaitement à vos intérêts
en jardinage et nature. Les ateliers de plantation proposés sont
idéaux pour votre niveau débutant."
⚙️ Engine: ollama+embeddings
⏱️ Temps: 7.2s
```

---

## 🧪 Test Python Indépendant

```bash
.\.venv\Scripts\python.exe test_recommender.py
```

**Sortie attendue** :

```
============================================================
TEST DU SYSTÈME DE RECOMMANDATION RAG
============================================================

🤖 Initialisation du recommender...
✅ Recommender initialisé avec succès

📊 DONNÉES DE TEST:
   Utilisateur: Test User
   Intérêts: jardinage, nature
   Activités préférées: plantation, entretien
   Nombre d'espaces verts: 3

🔍 ÉTAPE 1: Retrieval (Embeddings)...
   ✅ 2 candidats sélectionnés:
      1. Jardin Botanique (score: 0.694)
      2. Parc Central (score: 0.611)

🤖 ÉTAPE 2: Generation (Ollama)...
   ⚠️  Ollama peut prendre 5-30 secondes...
   ✅ Recommandation générée avec succès

============================================================
🎯 RÉSULTAT FINAL:
============================================================
   Meilleur choix: ID #2
   Score: 0.69
   Engine: ollama+embeddings
   Raison: [Explication générée par Ollama]
============================================================
```

---

## 🔍 Vérifications

### Ollama est-il actif ?

```bash
curl http://localhost:11434/api/tags
```

**Si ça échoue** : Pas de problème ! Le système utilisera le fallback (embeddings seuls)

### Les préférences sont-elles sauvegardées ?

```bash
php artisan tinker
>>> User::first()->preferences
```

### Les activités sont-elles dans les greenspaces ?

```bash
php artisan tinker
>>> GreenSpace::first()->activities
```

---

## 📁 Fichiers Importants

| Fichier                                       | Description                 |
| --------------------------------------------- | --------------------------- |
| `ai_recommender.py`                           | Script Python RAG principal |
| `app/Services/AIRecommender.php`              | Service Laravel             |
| `app/Services/OllamaService.php`              | Interface Ollama            |
| `resources/views/users/preferences.blade.php` | Page préférences            |
| `AI_RECOMMENDATION_DOCS.md`                   | Documentation complète      |

---

## 🎓 Architecture Simplifiée

```
User clique "Suggérer avec l'IA"
    ↓
Laravel ParticipationController
    ↓
AIRecommender Service (PHP)
    ↓
ai_recommender.py (Python)
    ↓
┌─────────────────┬──────────────────┐
│  Embeddings     │  Ollama LLM      │
│  (2-3 sec)      │  (5-10 sec)      │
│  Pré-filtrage   │  Décision finale │
└─────────────────┴──────────────────┘
    ↓
Résultat JSON avec best_match_id + reason
    ↓
Frontend affiche la recommandation
```

---

## 🐛 En cas de problème

### Erreur : "Ollama indisponible"

➡️ **Normal** si Ollama n'est pas démarré. Le fallback (embeddings seuls) prend le relais automatiquement.

### Erreur : "Python timeout"

➡️ **Première exécution** : Le modèle d'embedding (471 MB) se télécharge. Attendez 10 minutes.

### Erreur : "best_match_id manquant"

➡️ Vérifiez les logs : `tail -f storage/logs/laravel.log`

---

## 💡 Conseil Pro

**Pour de meilleures recommandations** :

1. ✅ Configurez vos préférences en détail
2. ✅ Ajoutez des activités variées aux greenspaces
3. ✅ Laissez Ollama actif pour les explications intelligentes

---

## 📞 Support

Consultez `AI_RECOMMENDATION_DOCS.md` pour la documentation complète.

**Bon test ! 🚀**
