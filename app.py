from flask import Flask, request, jsonify
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.neighbors import NearestNeighbors
import joblib

app = Flask(__name__)

MODEL_FILE = "recommendation_model.pkl"
VECTORIZER_FILE = "vectorizer.pkl"

# Training route
@app.route('/train', methods=['POST'])
def train():
    data = request.get_json()
    df = pd.DataFrame(data)

    # Combine features into one string
    df['features'] = (
        df['description'].fillna('') + " " +
        df['status'].fillna('') + " " +
        df['green_space'].apply(lambda x: x['type'] if x else '') + " " +
        df['association'].apply(lambda x: x['domain'] if x else '')
    )

    vectorizer = TfidfVectorizer()
    X = vectorizer.fit_transform(df['features'])

    model = NearestNeighbors(n_neighbors=3, metric='cosine')
    model.fit(X)

    joblib.dump(model, MODEL_FILE)
    joblib.dump(vectorizer, VECTORIZER_FILE)

    return jsonify({"message": "Model trained successfully", "total_projects": len(df)})

# Recommendation route
@app.route('/recommend', methods=['POST'])
def recommend():
    payload = request.get_json()
    description = payload.get("description", "")
    status = payload.get("status", "")
    green_space_type = payload.get("green_space_type", "")
    association_domain = payload.get("association_domain", "")

    vectorizer = joblib.load(VECTORIZER_FILE)
    model = joblib.load(MODEL_FILE)

    input_features = f"{description} {status} {green_space_type} {association_domain}"
    X_input = vectorizer.transform([input_features])

    distances, indices = model.kneighbors(X_input)
    return jsonify({"indices": indices.tolist(), "distances": distances.tolist()})

if __name__ == '__main__':
    app.run(port=5001, debug=True)
