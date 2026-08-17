import cv2
import numpy as np
import os
import glob

# Path to the uploaded image
image_path = r"C:\Users\LENOVO\.gemini\antigravity-ide\brain\d33a7133-0430-4b5a-9fd6-e09013f9b3dd\.user_uploaded\media_1786785069779.png"
if not os.path.exists(image_path):
    print("Image not found:", image_path)
    exit(1)

img = cv2.imread(image_path)
if img is None:
    print("Failed to read image")
    exit(1)

# Get image dimensions
h, w = img.shape[:2]

# The image contains a list of 9 logos in a table.
# Let's crop the image into 9 equal vertical slices, skipping the header.
header_height = int(h * 0.05) # Guessing header height
content_img = img[header_height:h, :]
ch, cw = content_img.shape[:2]

num_brands = 9
slice_height = ch // num_brands

out_dir = r"d:\UI design\public\images\brands"
os.makedirs(out_dir, exist_ok=True)

for i in range(num_brands):
    y_start = i * slice_height
    y_end = (i + 1) * slice_height
    # Crop the slice
    slice_img = content_img[y_start:y_end, :]
    
    # We can also crop the borders if needed, but a simple slice is fine
    out_path = os.path.join(out_dir, f"brand_{i+1}.png")
    cv2.imwrite(out_path, slice_img)

print("Saved 9 brand logos to", out_dir)
