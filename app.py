from flask import Flask, request, jsonify
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.neighbors import NearestNeighbors
import joblib
import json
import os
from flask_cors import CORS

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

MODEL_FILE = "recommendation_model.pkl"
VECTORIZER_FILE = "vectorizer.pkl"
PROJECTS_FILE = "projects.json"  # Store project data for later use


# 1️⃣ Training route
@app.route('/train', methods=['POST'])
def train():
    data = request.get_json()
    df = pd.DataFrame(data)

    if df.empty:
        return jsonify({"error": "No project data received"}), 400

    # Store project data locally for use during recommendations
    df.to_json(PROJECTS_FILE, orient="records", indent=2)

    # Combine important features into a single text string
    df['features'] = (
        df['description'].fillna('') + " " +
        df['status'].fillna('') + " " +
        df['green_space'].apply(lambda x: x.get('type', '') if isinstance(x, dict) else '') + " " +
        df['association'].apply(lambda x: x.get('domain', '') if isinstance(x, dict) else '')
    )

    # TF-IDF Vectorization
    vectorizer = TfidfVectorizer()
    X = vectorizer.fit_transform(df['features'])

    # Nearest Neighbors Model
    model = NearestNeighbors(n_neighbors=3, metric='cosine')
    model.fit(X)

    # Save model + vectorizer
    joblib.dump(model, MODEL_FILE)
    joblib.dump(vectorizer, VECTORIZER_FILE)

    return jsonify({
        "message": "Model trained successfully",
        "total_projects": len(df)
    })


# 2️⃣ Recommendation route
@app.route('/recommend', methods=['POST'])
def recommend():
    if not os.path.exists(PROJECTS_FILE):
        return jsonify({"error": "Model not trained yet. Please call /train first."}), 400

    payload = request.get_json()
    description = payload.get("description", "")
    status = payload.get("status", "")
    green_space_type = payload.get("green_space_type", "")
    association_domain = payload.get("association_domain", "")

    # Load saved model + vectorizer + project data
    vectorizer = joblib.load(VECTORIZER_FILE)
    model = joblib.load(MODEL_FILE)
    with open(PROJECTS_FILE, "r") as f:
        projects_data = json.load(f)

    # Transform input into feature vector
    input_features = f"{description} {status} {green_space_type} {association_domain}"
    X_input = vectorizer.transform([input_features])

    # Get top recommendations
    distances, indices = model.kneighbors(X_input)

    recommended_projects = []
    for idx, dist in zip(indices[0], distances[0]):
        project = projects_data[idx]
        recommended_projects.append({
            "id": project.get("id"),
            "title": project.get("title"),
            "description": project.get("description"),
            "estimated_budget": project.get("estimated_budget"),
            "status": project.get("status"),
            "green_space": project.get("green_space", {}).get("name", None),
            "association": project.get("association", {}).get("name", None),
            "similarity_score": float(1 - dist)  # Convert cosine distance to similarity
        })

    return jsonify({"recommendations": recommended_projects})


if __name__ == '__main__':
    app.run(port=5001, debug=True)
