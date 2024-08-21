# Import necessary libraries
from flask import Flask, Response
import cv2
import mediapipe as mp
import numpy as np
import pickle
import pandas as pd
import os
from datetime import datetime, timedelta
import warnings
warnings.filterwarnings('ignore', category=UserWarning, module='sklearn')
import mysql.connector

# Establish MySQL connection
mydb = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="thesis_bscs2024"
)

app = Flask(__name__)

mp_drawing = mp.solutions.drawing_utils
mp_holistic = mp.solutions.holistic

model = None
with open('body_language.pkl', 'rb') as f:
    model = pickle.load(f)

# Initialize variables for tracking actions
previous_classes = []
action_counts = {'looking_around': 0, 'looking_down': 0, 'holding_pocket': 0}
alternation_limit = 2  # Adjust as needed
message_displayed = False
message_display_duration = 30  # Number of frames to display the message
message_frame_count = 0

# Initialize time tracking for looking down action
start_time_looking_down = None
duration_threshold = timedelta(seconds=3)

# Set the snapshot directory
snapshot_dir = r'C:\Users\evand\Mega\Projects\Thesis\snapshots'
print(f"Snapshot directory: {snapshot_dir}")
if not os.path.exists(snapshot_dir):
    os.makedirs(snapshot_dir)
    print(f"Directory created: {snapshot_dir}")
else:
    print(f"Directory already exists: {snapshot_dir}")

def generate_frames():
    global previous_classes, action_counts, message_displayed, message_frame_count, start_time_looking_down

    # data input
    cap = cv2.VideoCapture(0)  # Adjust camera index as needed
    frame_width = int(cap.get(cv2.CAP_PROP_FRAME_WIDTH))
    frame_height = int(cap.get(cv2.CAP_PROP_FRAME_HEIGHT))
    

    with mp_holistic.Holistic(min_detection_confidence=0.5, min_tracking_confidence=0.5) as holistic:
        while cap.isOpened():
            ret, frame = cap.read()
            if not ret:
                break
            
            # Recolor Feed
            image = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            image.flags.writeable = False        

            # Make Detections
            results = holistic.process(image)

            # Recolor image back to BGR for rendering
            image.flags.writeable = True   
            image = cv2.cvtColor(image, cv2.COLOR_RGB2BGR)

            mp_drawing.draw_landmarks(image, results.right_hand_landmarks, mp_holistic.HAND_CONNECTIONS)
            mp_drawing.draw_landmarks(image, results.left_hand_landmarks, mp_holistic.HAND_CONNECTIONS)
            mp_drawing.draw_landmarks(image, results.pose_landmarks, mp_holistic.POSE_CONNECTIONS)

            try:
                pose = results.pose_landmarks.landmark 
                pose_row = list(np.array([[landmark.x, landmark.y, landmark.z, landmark.visibility] for landmark in pose]).flatten())
                
                # make detections
                X = pd.DataFrame([pose_row])
                body_language_class = model.predict(X)[0]
                body_language_prob = model.predict_proba(X)[0]

                # coords for display
                coords = tuple(np.multiply(
                    np.array(
                        (results.pose_landmarks.landmark[mp_holistic.PoseLandmark.LEFT_EAR].x, 
                         results.pose_landmarks.landmark[mp_holistic.PoseLandmark.LEFT_EAR].y)),
                    [640, 480]).astype(int))

                # font and display control
                cv2.rectangle(image, (coords[0], coords[1]+5), 
                              (coords[0]+len(body_language_class)*20, coords[1]-30), 
                              (245, 117, 16), -1)
                cv2.putText(image, body_language_class, coords, 
                            cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 255), 2, cv2.LINE_AA)
                
                # status box
                center_x = frame_width // 2
                cv2.rectangle(image, (center_x - 125, 100), (center_x + 125, 60), (245, 117, 16), -1)

                # actual status box
                cv2.putText(image, 'CLASS', (center_x - 30, 100), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 0), 1, cv2.LINE_AA)
                cv2.putText(image, body_language_class.split(' ')[0], (center_x - 35, 40), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 255), 2, cv2.LINE_AA)
                cv2.putText(image, 'PROB', (center_x - 110, 100), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 0), 1, cv2.LINE_AA)
                cv2.putText(image, str(round(body_language_prob[np.argmax(body_language_prob)], 2)), (center_x - 115, 40), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 255), 2, cv2.LINE_AA)

                # Track actions for potential theft detection
                previous_classes.append(body_language_class)
                if len(previous_classes) > 10:
                    previous_classes.pop(0)

                # Looking around logic
                if len(set(previous_classes[-3:])) > 1 and all(c in ['left', 'right', 'down'] for c in previous_classes[-3:]):
                    action_counts['looking_around'] += 1
                    previous_classes = []  # Reset to track next sequence

                # Holding pocket logic
                if body_language_class == 'pocket':
                    action_counts['holding_pocket'] += 1

                # Looking down logic
                if body_language_class == 'down':
                    if start_time_looking_down is None:
                        start_time_looking_down = datetime.now()
                    elif datetime.now() - start_time_looking_down >= duration_threshold:
                        action_counts['looking_down'] += 1
                        start_time_looking_down = None  # Reset after counting
                else:
                    start_time_looking_down = None

                # Check for potential theft conditions
                if sum(value >= 1 for value in action_counts.values()) >= 2:
                    current_time = datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
                    # Save snapshot to the specified directory with timestamped filename
                    file_name = f"snapshot_{current_time}.jpg"
                    file_path = os.path.join(snapshot_dir, file_name)
                    print(f"Saving snapshot at {file_path}")
                    success = cv2.imwrite(file_path, image)
                    if success:
                        print(f"Snapshot saved successfully at {file_path}")
                    else:
                        print(f"Failed to save snapshot at {file_path}")

                    # Insert data into MySQL database
                    try:
                        cursor = mydb.cursor()
                        sql = "INSERT INTO trains (motion_name, threshold, level, created_at, updated_at) VALUES (%s, %s, %s, %s, %s)"
                        threshold_value = float(round(body_language_prob[np.argmax(body_language_prob)], 2))
                        val = (current_time, threshold_value, "potential_theft", current_time, current_time)
                        cursor.execute(sql, val)
                        mydb.commit()
                        print("Data inserted into MySQL database")
                    except Exception as e:
                        print("Error inserting data into MySQL database:", e)
                    finally:
                        cursor.close()

                    message_displayed = True 
                    message_frame_count = 0
                    action_counts = {'looking_around': 0, 'looking_down': 0, 'holding_pocket': 0}  # Reset counters

            except Exception as e:
                print(e)
                pass

            # Display the frame and message if needed
            if message_displayed:
                cv2.putText(image, "Potential Theft Detected!", (200, 200), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 3, cv2.LINE_AA)
                message_frame_count += 1
                if message_frame_count > message_display_duration:
                    message_displayed = False

            # Display the action counts in the center of the screen
            action_text = f"Looking around: {action_counts['looking_around']} | Looking down: {action_counts['looking_down']} | Holding pocket: {action_counts['holding_pocket']}"
            text_size = cv2.getTextSize(action_text, cv2.FONT_HERSHEY_SIMPLEX, 0.6, 2)[0]
            text_x = (frame_width - text_size[0]) // 2
            text_y = (frame_height - text_size[1]) // 2
            cv2.putText(image, action_text, (text_x, text_y), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2, cv2.LINE_AA)

            ret, buffer = cv2.imencode('.jpg', image)
            frame = buffer.tobytes()
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

    cap.release()

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(debug=True)
