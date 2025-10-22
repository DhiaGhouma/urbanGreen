import requests
from datetime import datetime
import json

def get_weather(lat, lng, api_key):
    url = f"http://api.openweathermap.org/data/2.5/weather?lat={lat}&lon={lng}&appid={api_key}&units=metric"
    response = requests.get(url)
    return response.json()

def suggest_plants(temperature, season, soil_type="normal"):
    plants_database = {
        "spring": {
            "cold": [
                {"name": "Pensées", "type": "Fleur", "ideal_temp": "5-15°C"},
                {"name": "Primevères", "type": "Fleur", "ideal_temp": "5-15°C"}
            ],
            "moderate": [
                {"name": "Lavande", "type": "Arbuste", "ideal_temp": "15-25°C"},
                {"name": "Rosier", "type": "Arbuste", "ideal_temp": "15-25°C"}
            ],
            "warm": [
                {"name": "Géranium", "type": "Fleur", "ideal_temp": "20-30°C"},
                {"name": "Pétunia", "type": "Fleur", "ideal_temp": "20-30°C"}
            ]
        },
        "summer": {
            "moderate": [
                {"name": "Dahlia", "type": "Fleur", "ideal_temp": "15-25°C"},
                {"name": "Œillet", "type": "Fleur", "ideal_temp": "15-25°C"}
            ],
            "warm": [
                {"name": "Lantana", "type": "Arbuste", "ideal_temp": "20-35°C"},
                {"name": "Verveine", "type": "Fleur", "ideal_temp": "20-35°C"}
            ],
            "hot": [
                {"name": "Pourpier", "type": "Plante grasse", "ideal_temp": ">30°C"},
                {"name": "Gazania", "type": "Fleur", "ideal_temp": ">30°C"}
            ]
        },
        "autumn": {
            "cold": [
                {"name": "Chrysanthème", "type": "Fleur", "ideal_temp": "5-15°C"},
                {"name": "Aster", "type": "Fleur", "ideal_temp": "5-15°C"}
            ],
            "moderate": [
                {"name": "Cyclamen", "type": "Fleur", "ideal_temp": "10-20°C"},
                {"name": "Heuchère", "type": "Vivace", "ideal_temp": "10-20°C"}
            ]
        },
        "winter": {
            "cold": [
                {"name": "Hellébore", "type": "Fleur", "ideal_temp": "0-10°C"},
                {"name": "Bruyère d'hiver", "type": "Arbuste", "ideal_temp": "0-10°C"}
            ]
        }
    }

    # Déterminer la catégorie de température
    if temperature < 10:
        temp_category = "cold"
    elif temperature < 20:
        temp_category = "moderate"
    elif temperature < 30:
        temp_category = "warm"
    else:
        temp_category = "hot"

    # Obtenir les suggestions pour la saison et la température
    season_plants = plants_database.get(season, {})
    suggested_plants = season_plants.get(temp_category, [])

    return suggested_plants

def get_current_season():
    month = datetime.now().month
    if month in [3, 4, 5]:
        return "spring"
    elif month in [6, 7, 8]:
        return "summer"
    elif month in [9, 10, 11]:
        return "autumn"
    else:
        return "winter"

def get_suggestions(lat, lng):
    try:
        # Remplacez par votre clé API OpenWeatherMap
        api_key = "893f03f61365078db3c202b1b12eaca1"
        weather_data = get_weather(lat, lng, api_key)
        
        temperature = weather_data['main']['temp']
        season = get_current_season()
        
        suggestions = suggest_plants(temperature, season)
        
        return {
            "success": True,
            "temperature": temperature,
            "season": season,
            "suggestions": suggestions
        }
    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }

# Pour test direct
if __name__ == "__main__":
    print(get_suggestions(48.8566, 2.3522))  