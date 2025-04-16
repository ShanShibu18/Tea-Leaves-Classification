from flask import Flask, request, jsonify
from flask_cors import CORS  # Import CORS

import tensorflow as tf
import numpy as np
import json
import os
import datetime
from tensorflow.keras.preprocessing import image

# Load the trained model
model = tf.keras.models.load_model('model/tea_leaf_model.h5')

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Class labels
CLASS_NAMES = ["Bad Tea Leaf", "Good Tea Leaf", "Not Tea Leaf"]

# File to store history
HISTORY_FILE = "history.json"

# Ensure history file exists
if not os.path.exists(HISTORY_FILE):
    with open(HISTORY_FILE, "w") as f:
        json.dump([], f)

@app.route('/predict', methods=['POST'])
def predict():
    if 'file' not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    file = request.files['file']
    file_path = os.path.join("uploads", file.filename)  # Save uploaded file
    file.save(file_path)

    # Preprocess the image
    img = image.load_img(file_path, target_size=(299, 299))
    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0) / 255.0  # Normalize

    # Make prediction
    prediction = model.predict(img_array)
    predicted_class = np.argmax(prediction)  # Get index of highest probability
    result = CLASS_NAMES[predicted_class]  # Map index to class name

    # Get timestamp
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    # Read current history
    with open(HISTORY_FILE, "r") as f:
        history = json.load(f)

    # Append new entry (latest entry first)
    history.insert(0, {"image": file.filename, "result": result, "date": timestamp})

    # Save updated history
    with open(HISTORY_FILE, "w") as f:
        json.dump(history, f, indent=4)

    return jsonify({"prediction": result, "timestamp": timestamp})

@app.route('/history', methods=['GET'])
def get_history():
    with open(HISTORY_FILE, "r") as f:
        history = json.load(f)
    return jsonify(history)

if __name__ == '__main__':
    os.makedirs("uploads", exist_ok=True)
    app.run(debug=True, port=5001)