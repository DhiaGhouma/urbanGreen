#!/usr/bin/env python3
"""
suggestionPlants.py

Usage:
    python suggestionPlants.py <latitude> <longitude>

This script fetches current weather (temperature) for the provided latitude/longitude
using the Open-Meteo free API, determines the local season, and returns a JSON
payload with plant suggestions adapted to the temperature and season.

Output (to stdout): JSON with the following structure:
{
  "success": true,
  "temperature": 21.3,
  "season": "spring",
  "suggestions": [
      {"name": "Lavender", "type": "shrub", "ideal_temp": "15-25", "notes": "Prefers well-drained soil"},
      ...
  ]
}

If the weather API call fails the script will still try to make suggestions based
on the current month and a reasonable default temperature.

Requirements:
    pip install requests python-dateutil

You can extend the PLANT_DB below with your own dataset or connect to a database.
"""

import sys
import json
import math
from datetime import datetime
from dateutil import tz
import requests

# -------------------- Configuration / DB --------------------
# A simple local database of plants. Extend as needed.
PLANT_DB = [
    {"name": "Lavender", "type": "shrub", "min_temp": 10, "max_temp": 30, "seasons": ["spring","summer"], "notes": "Préfère sol bien drainé, exposition ensoleillée."},
    {"name": "Olivier", "type": "tree", "min_temp": 5, "max_temp": 35, "seasons": ["spring","summer"], "notes": "Résistant à la sécheresse, climat méditerranéen."},
    {"name": "Rosier", "type": "shrub", "min_temp": 5, "max_temp": 28, "seasons": ["spring","summer"], "notes": "Tailler après la floraison."},
    {"name": "Géranium", "type": "flower", "min_temp": 10, "max_temp": 30, "seasons": ["spring","summer","autumn"], "notes": "Bonne plante pour bacs et massifs."},
    {"name": "Chêne pédonculé", "type": "tree", "min_temp": -10, "max_temp": 30, "seasons": ["spring","summer","autumn"], "notes": "Arbre robuste, apprécie sols profonds."},
    {"name": "Fougère", "type": "groundcover", "min_temp": 8, "max_temp": 25, "seasons": ["spring","autumn"], "notes": "Aime l'ombre et l'humidité."},
    {"name": "Thym", "type": "herb", "min_temp": 8, "max_temp": 35, "seasons": ["spring","summer"], "notes": "Parfait pour rocailles et sols pauvres."},
    {"name": "Bambou", "type": "grass", "min_temp": 5, "max_temp": 35, "seasons": ["spring","summer"], "notes": "Végétation persistante, nécessite espace."},
    {"name": "Hydrangée", "type": "shrub", "min_temp": 5, "max_temp": 25, "seasons": ["spring","summer","autumn"], "notes": "Apprécie sols frais et humides."},
    {"name": "Lavande papillon (Buddleja)", "type": "shrub", "min_temp": 5, "max_temp": 30, "seasons": ["spring","summer"], "notes": "Attire les papillons."},
]

# -------------------- Helpers --------------------

def get_current_weather(lat, lon):
    """Query Open-Meteo for current weather. Returns dict with temperature (C)
    and timezone name, or raises Exception on failure."""
    url = (
        f"https://api.open-meteo.com/v1/forecast?latitude={lat}&longitude={lon}"
        "&current_weather=true&timezone=auto"
    )
    resp = requests.get(url, timeout=10)
    resp.raise_for_status()
    data = resp.json()
    if 'current_weather' in data and 'temperature' in data['current_weather']:
        return {
            'temperature': float(data['current_weather']['temperature']),
            'timezone': data.get('timezone', 'UTC')
        }
    raise Exception('Unexpected weather API response')


def month_to_season(month, latitude):
    """Return season name based on month (northern/southern hemisphere).
    Seasons: winter, spring, summer, autumn
    month: 1-12"""
    # Northern hemisphere seasons
    if latitude >= 0:
        if month in (12, 1, 2):
            return 'winter'
        if month in (3, 4, 5):
            return 'spring'
        if month in (6, 7, 8):
            return 'summer'
        return 'autumn'
    else:
        # Southern hemisphere months invert
        if month in (12, 1, 2):
            return 'summer'
        if month in (3, 4, 5):
            return 'autumn'
        if month in (6, 7, 8):
            return 'winter'
        return 'spring'


def score_plant(plant, temp, season):
    """Compute a simple score 0..100 for a plant given temperature and season.
    Higher means better match."""
    score = 0
    # Temperature scoring: if within range -> +60, otherwise penalize by distance
    if plant['min_temp'] <= temp <= plant['max_temp']:
        score += 60
    else:
        # distance from ideal range (in degrees)
        if temp < plant['min_temp']:
            diff = plant['min_temp'] - temp
        else:
            diff = temp - plant['max_temp']
        # reduce score proportionally (cap)
        temp_penalty = min(50, diff * 5)
        score += max(0, 60 - temp_penalty)

    # Season bonus
    if season in plant.get('seasons', []):
        score += 30

    # small diversity bonus for shrubs/trees to favour greening
    if plant['type'] in ('tree', 'shrub'):
        score += 5

    # clamp
    return int(max(0, min(100, score)))


# -------------------- Main --------------------

def main(argv):
    if len(argv) < 3:
        print(json.dumps({'success': False, 'error': 'Usage: suggestionPlants.py <lat> <lon>'}))
        return 2

    try:
        lat = float(argv[1])
        lon = float(argv[2])
    except ValueError:
        print(json.dumps({'success': False, 'error': 'Latitude and longitude must be numbers.'}))
        return 2

    # Default fallbacks
    temperature = None
    timezone_name = 'UTC'
    now = datetime.utcnow()

    try:
        weather = get_current_weather(lat, lon)
        temperature = weather['temperature']
        timezone_name = weather.get('timezone', 'UTC') or 'UTC'
        # try to convert to local date if timezone provided
        try:
            local_tz = tz.gettz(timezone_name)
            if local_tz:
                now = datetime.now(tz=local_tz)
        except Exception:
            pass
    except Exception as e:
        # Log to stderr and keep going with defaults
        # (The Laravel side logs errors; here we only return a usable response)
        # print to stderr so process logs will contain the error
        print(json.dumps({'success': False, 'error': 'Weather API failed', 'detail': str(e)}))
        # fallback: choose a reasonable default temperature based on latitude and month
        month = now.month
        # simple heuristic: warmer near equator
        equator_factor = max(0, 1 - (abs(lat) / 90))
        base_temp = 15 + equator_factor * 12  # between ~15 and ~27
        # add seasonal variation
        if month in (6,7,8):
            base_temp += 5
        if month in (12,1,2):
            base_temp -= 5
        temperature = round(base_temp, 1)

    month = now.month
    season = month_to_season(month, lat)

    # Score plants
    scored = []
    for p in PLANT_DB:
        sc = score_plant(p, temperature, season)
        item = {
            'name': p['name'],
            'type': p['type'],
            'ideal_temp': f"{p['min_temp']}-{p['max_temp']}",
            'seasons': p.get('seasons', []),
            'notes': p.get('notes', ''),
            'score': sc
        }
        scored.append(item)

    # Sort by score desc and return top 6
    scored_sorted = sorted(scored, key=lambda x: x['score'], reverse=True)[:6]

    out = {
        'success': True,
        'temperature': temperature,
        'season': season,
        'timezone': timezone_name,
        'suggestions': scored_sorted
    }

    print(json.dumps(out, ensure_ascii=False))
    return 0


if __name__ == '__main__':
    sys.exit(main(sys.argv))
