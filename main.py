from colorthief import ColorThief

color_thief = ColorThief('my_image.jpeg')
# get the dominant color
dominant_color = color_thief.get_color(quality=1)
# build a color palette
palette = color_thief.get_palette(color_count=10)

print(palette)



# import colorgram

# # Extract 6 colors from an image.
# colors = colorgram.extract('download_1.png', 4)
# for color in colors:
#     print(color.rgb, end="\n")
# # print(colors[0].Color.rgb)

# # colorgram.extract returns Color objects, which let you access
# # RGB, HSL, and what proportion of the image was that color.
# first_color = colors[0]
# rgb = first_color.rgb # e.g. (255, 151, 210)
# hsl = first_color.hsl # e.g. (230, 255, 203)
# proportion  = first_color.proportion # e.g. 0.34


# print(rgb)

# # RGB and HSL are named tuples, so values can be accessed as properties.
# # These all work just as well:
# red = rgb[0]
# red = rgb.r
# saturation = hsl[1]
# saturation = hsl.s


# print(saturation)

# import numpy as np
# import pandas as pd
# import matplotlib.pyplot as plt
# import matplotlib.patches as patches
# import matplotlib.image as mpimg

# from PIL import Image
# from matplotlib.offsetbox import OffsetImage, AnnotationBbox

# import cv2
# import extcolors

# from colormap import rgb2hex



# input_name = '<photo location/name>'
# output_width = 900                   #set the output size
# img = Image.open(input_name)
# wpercent = (output_width/float(img.size[0]))
# hsize = int((float(img.size[1])*float(wpercent)))
# img = img.resize((output_width,hsize), Image.ANTIALIAS)

# #save
# resize_name = 'resize_' + input_name  #the resized image name
# img.save(resize_name)                 #output location can be specified before resize_name

# #read
# plt.figure(figsize=(9, 9))
# img_url = resize_name
# img = plt.imread(img_url)
# plt.imshow(img)
# plt.axis('off')
# plt.show()

# colors_x = extcolors.extract_from_path(img_url, tolerance = 12, limit = 12)
# colors_x


# # from collections import Counter 
# # from sklearn.cluster import KMeans 
# # from matplotlib import colors 
# # import matplotlib.pyplot as plt 
# # import numpy as np 
# # import cv2


# # def preprocess(raw):
# #     image = cv2.resize(raw, (900, 600), interpolation = cv2.INTER_AREA)                                          
# #     image = image.reshape(image.shape[0]*image.shape[1], 3)
# #     return image

# # def rgb_to_hex(rgb_color):
# #     hex_color = "#"
# #     for i in rgb_color:
# #         hex_color += ("{:02x}".format(int(i)))
# #     return hex_color

# # def analyze(img):
# #     clf = KMeans(n_clusters = 5)
# #     color_labels = clf.fit_predict(img)
# #     center_colors = clf.cluster_centers_
# #     counts = Counter(color_labels)
# #     ordered_colors = [center_colors[i] for i in counts.keys()]
# #     hex_colors = [rgb_to_hex(ordered_colors[i]) for i in counts.keys()]

# #     plt.figure(figsize = (12, 8))
# #     plt.pie(counts.values(), labels = hex_colors, colors = hex_colors)

# #     plt.savefig("my_pie.png")
# #     print("Found the following colors:\n")
# #     for color in hex_colors:
# #       print(color)

      


# # image = cv2.imread('my_image.jpeg') 
# # image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)

# # modified_image = preprocess(image) 



# # analyze(modified_image)