import cv2
import numpy as np
import matplotlib.pyplot as plt
from collections import deque

buffer_size = 64
pts = deque(maxlen=buffer_size)

cap = cv2.VideoCapture(0)

plt.ion()
fig, ax = plt.subplots()
x_vals, y_vals = [], []
line, = ax.plot([], [], 'r-')
ax.set_xlim(0, buffer_size)
ax.set_ylim(-300, 300)

while True:
    ret, frame = cap.read()
    if not ret:
        break

    frame = cv2.resize(frame, (640, 480))

    hsv = cv2.cvtColor(frame, cv2.COLOR_BGR2HSV)

    lower_red1 = np.array([0, 120, 70])
    upper_red1 = np.array([10, 255, 255])
    lower_red2 = np.array([170, 120, 70])
    upper_red2 = np.array([180, 255, 255])

    mask1 = cv2.inRange(hsv, lower_red1, upper_red1)
    mask2 = cv2.inRange(hsv, lower_red2, upper_red2)

    mask = mask1 + mask2

    mask = cv2.erode(mask, None, iterations=2)
    mask = cv2.dilate(mask, None, iterations=2)

    contours, _ = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

    center = None

    if contours:
        c = max(contours, key=cv2.contourArea)
        ((x, y), radius) = cv2.minEnclosingCircle(c)
        M = cv2.moments(c)

        if M["m00"] > 0:
            center = (int(M["m10"] / M["m00"]), int(M["m01"] / M["m00"]))

            if radius > 5:
                cv2.circle(frame, (int(x), int(y)), int(radius), (0, 255, 255), 2)
                cv2.circle(frame, center, 5, (0, 0, 255), -1)

    pts.appendleft(center)

    for i in range(1, len(pts)):
        if pts[i - 1] is None or pts[i] is None:
            continue
        thickness = int(np.sqrt(buffer_size / float(i + 1)) * 2.5)
        cv2.line(frame, pts[i - 1], pts[i], (0, 0, 255), thickness)

    if center:
        x_vals.append(len(x_vals))
        y_vals.append(center[1])
        line.set_data(x_vals[-buffer_size:], y_vals[-buffer_size:])
        ax.set_xlim(max(0, len(x_vals) - buffer_size), len(x_vals))
        ax.set_ylim(min(y_vals[-buffer_size:], default=0) - 50, max(y_vals[-buffer_size:], default=0) + 50)
        plt.draw()
        plt.pause(0.001)

    cv2.imshow("Frame", frame)
    cv2.imshow("Mask", mask)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()