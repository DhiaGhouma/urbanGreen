import sys
import time
import requests

print("Testing Ollama direct call...", file=sys.stderr)
start = time.time()

try:
    response = requests.post(
        "http://127.0.0.1:11434/api/generate",
        json={
            "model": "llama3.1",
            "prompt": "Réponds en 10 mots max: Pourquoi le jardin botanique est bon pour le jardinage?",
            "stream": False,
            "options": {
                "temperature": 0.3,
                "num_predict": 50,
            }
        },
        timeout=30
    )
    response.raise_for_status()
    result = response.json()
    elapsed = time.time() - start
    print(f"✅ Success in {elapsed:.2f}s", file=sys.stderr)
    print(f"Response: {result['response'][:200]}", file=sys.stderr)
except Exception as e:
    elapsed = time.time() - start
    print(f"❌ Error after {elapsed:.2f}s: {e}", file=sys.stderr)
