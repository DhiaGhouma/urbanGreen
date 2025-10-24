#!/usr/bin/env python3
"""
SuggestionsPlants.py

Application IA Flask pour suggérer des plantes adaptées à la température,
la latitude, la longitude et la saison, à l'aide d'un modèle RandomForestClassifier.

Étapes :
1. Entraîne automatiquement un modèle ML (plant_model.pkl s'il n'existe pas)
2. Lance une API Flask sur /suggest pour prédire la plante la plus adaptée

Utilisation :
    python SuggestionsPlants.py
    -> API sur http://127.0.0.1:5000/suggest?lat=36.8&lon=10.2
"""

import os
import joblib
import numpy as np
import pandas as pd
import requests
from flask import Flask, request, jsonify
from datetime import datetime
from dateutil import tz
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split

# -----------------------------------------------------------------
# BASE DE DONNÉES DES PLANTES
# -----------------------------------------------------------------
PLANT_DB = [
    {"name": "Lavender", "type": "shrub", "min_temp": 10, "max_temp": 30, "seasons": ["spring","summer"]},
    {"name": "Olivier", "type": "tree", "min_temp": 5, "max_temp": 35, "seasons": ["spring","summer"]},
    {"name": "Rosier", "type": "shrub", "min_temp": 5, "max_temp": 28, "seasons": ["spring","summer"]},
    {"name": "Géranium", "type": "flower", "min_temp": 10, "max_temp": 30, "seasons": ["spring","summer","autumn"]},
    {"name": "Chêne pédonculé", "type": "tree", "min_temp": -10, "max_temp": 30, "seasons": ["spring","summer","autumn"]},
    {"name": "Fougère", "type": "groundcover", "min_temp": 8, "max_temp": 25, "seasons": ["spring","autumn"]},
    {"name": "Thym", "type": "herb", "min_temp": 8, "max_temp": 35, "seasons": ["spring","summer"]},
    {"name": "Bambou", "type": "grass", "min_temp": 5, "max_temp": 35, "seasons": ["spring","summer"]},
    {"name": "Hydrangée", "type": "shrub", "min_temp": 5, "max_temp": 25, "seasons": ["spring","summer","autumn"]},
    {"name": "Lavande papillon (Buddleja)", "type": "shrub", "min_temp": 5, "max_temp": 30, "seasons": ["spring","summer"]},
]

SEASON_MAP = {'winter': 0, 'spring': 1, 'summer': 2, 'autumn': 3}
MODEL_PATH = "plant_model.pkl"

# -----------------------------------------------------------------
# FONCTION D’ENTRAÎNEMENT DU MODÈLE
# -----------------------------------------------------------------
def train_model():
    print("🌿 Entraînement du modèle IA en cours...")
    rows = []

    for plant in PLANT_DB:
        for s in plant["seasons"]:
            for temp in range(plant["min_temp"], plant["max_temp"], 3):
                rows.append({
                    "temperature": temp,
                    "season": SEASON_MAP[s],
                    "latitude": 36.8,
                    "longitude": 10.2,
                    "plant_name": plant["name"]
                })

    df = pd.DataFrame(rows)
    X = df[["latitude", "longitude", "temperature", "season"]]
    y = df["plant_name"]

    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    model = RandomForestClassifier(n_estimators=100, random_state=42)
    model.fit(X_train, y_train)
    acc = model.score(X_test, y_test)
    print(f"✅ Modèle entraîné avec précision : {acc:.2f}")

    joblib.dump(model, MODEL_PATH)
    print(f"📦 Modèle sauvegardé sous '{MODEL_PATH}'")

# -----------------------------------------------------------------
# CHARGEMENT DU MODÈLE (ou entraînement si inexistant)
# -----------------------------------------------------------------
if not os.path.exists(MODEL_PATH):
    train_model()

model = joblib.load(MODEL_PATH)
print("🤖 Modèle IA chargé avec succès.")

# -----------------------------------------------------------------
# FONCTIONS UTILITAIRES
# -----------------------------------------------------------------
def month_to_season(month, latitude):
    if latitude >= 0:
        if month in (12, 1, 2): return 'winter'
        if month in (3, 4, 5): return 'spring'
        if month in (6, 7, 8): return 'summer'
        return 'autumn'
    else:
        if month in (12, 1, 2): return 'summer'
        if month in (3, 4, 5): return 'autumn'
        if month in (6, 7, 8): return 'winter'
        return 'spring'

def get_current_weather(lat, lon):
    url = (
        f"https://api.open-meteo.com/v1/forecast?latitude={lat}&longitude={lon}"
        "&current_weather=true&timezone=auto"
    )
    resp = requests.get(url, timeout=10)
    data = resp.json()
    temp = data["current_weather"]["temperature"]
    tz_name = data.get("timezone", "UTC")
    return temp, tz_name

# -----------------------------------------------------------------
# API FLASK
# -----------------------------------------------------------------
app = Flask(__name__)

@app.route('/suggest', methods=['GET'])
def suggest_plants():
    try:
        lat = float(request.args.get("lat"))
        lon = float(request.args.get("lon"))
    except (TypeError, ValueError):
        return jsonify({"success": False, "error": "Paramètres lat/lon invalides"}), 400

    # Récupération météo
    try:
        temp, tz_name = get_current_weather(lat, lon)
        local_tz = tz.gettz(tz_name)
        now = datetime.now(local_tz)
    except Exception:
        temp = 20.0
        tz_name = "UTC"
        now = datetime.utcnow()

    # Saison actuelle
    season_name = month_to_season(now.month, lat)
    season_val = SEASON_MAP[season_name]

    # Prédiction
    X = np.array([[lat, lon, temp, season_val]])
    prediction = model.predict(X)[0]
    proba = model.predict_proba(X).max()

    return jsonify({
        "success": True,
        "latitude": lat,
        "longitude": lon,
        "temperature": temp,
        "season": season_name,
        "suggested_plant": prediction,
        "confidence": round(float(proba), 3)
    })

# -----------------------------------------------------------------
# LANCEMENT DU SERVEUR
# -----------------------------------------------------------------
if __name__ == '__main__':
    app.run(debug=True)
