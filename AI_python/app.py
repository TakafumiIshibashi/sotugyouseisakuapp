from PIL import Image
import numpy as np

import pymysql
from pymysql import cursors
from skimage.io import imread
import tensorflow as tf


connection = pymysql.connect(
    host="",
    db="",
    user="",
    passwd="",
    charset="utf8",
    port=3306,
    cursorclass=pymysql.cursors.DictCursor
)



sql = "SELECT * FROM python_table INNER JOIN photo_table ON python_table.photo_id = photo_table.photo_id WHERE come_updated_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND come_updated_at <= current_date()"
cursor = connection.cursor()
cursor.execute(sql)
python_table = cursor.fetchall()



for thema in python_table:
    
    posted_at_img = {thema["photo_id"]:thema["posted_at"]}
    list_a = [posted_at_img]
    # print(a,end=",")
    # print(a)


    
# print(python_table[0]["posted_at"])
# q = int(len(python_table))
imlist = []
for i in range(int(len(python_table))):
    imlist.append(python_table[i]["posted_at"])
# print(imlist)


# 配列のURLを画像数値として配列
imtest1 = []
for i in range(int(len(imlist))):
    imtest1.append(imread(imlist[i]))
# print(imtest1)

# 画像数値として配列した値を画像化
imtest2 =[]
for i in range(int(len(imtest1))):
    imtest2.append(Image.fromarray(np.uint8(imtest1[i])))
# print(imtest2)


imtest3 = []
for i in range(int(len(imtest2))):
    imtest3.append(imtest2[i].convert('RGB'))
# print(imtest3)


# RGBに変換した画像を100,100に
imtest4 = []
for i in range(int(len(imtest3))):
    imtest4.append(imtest3[i].resize((100, 100)))
# print(imtest4)


# 画像を数値化
X = []
for i in range(int(len(imtest4))):
    data = np.asarray(imtest4[i])
    X.append(data)

X = np.array(X)
# print(X)

X = X.astype('float32') / 255

from keras import backend as K


def root_mean_squared_error(y_true, y_pred):
    return K.sqrt(K.mean(K.square(y_pred - y_true), axis=-1))


model = tf.keras.models.load_model('model-opt.hdf5', custom_objects={
                                   'root_mean_squared_error': root_mean_squared_error})
# print(model)

# testデータの予測値
preds = model.predict(X[0:int(len(X))])
# print(preds)

# データベースに予測値をアップデート
for i in range(int(len(imlist))):
    a = float(preds[i])
    b = imlist[i]
    with connection.cursor() as cursor:
        sql = "UPDATE python_table SET python_point = %s WHERE posted_at = %s"
        r = cursor.execute(sql, (a, b))
        print(r)  # -> 1
        # autocommitではないので、明示的にコミットする
        connection.commit()
        

cursor.close()
connection.close()
