import re
import io
import zipfile
import os
from tqdm import tqdm
import glob
from skimage.io import imread
from pymysql import cursors
import pymysql
from keras.optimizers import adam_v2
from keras.preprocessing.image import ImageDataGenerator
from keras.callbacks import ModelCheckpoint, EarlyStopping, TensorBoard, ReduceLROnPlateau
from keras.utils.np_utils import to_categorical
from keras.layers.pooling import GlobalAveragePooling2D
from keras.layers.core import Dense
from keras.models import Model, load_model
from keras.applications.xception import Xception
from sklearn.model_selection import train_test_split
from PIL import Image
import matplotlib.pyplot as plt
import pandas as pd
import numpy as np
from keras import backend as K
from keras.optimizers import gradient_descent_v2
from keras.optimizers import rmsprop_v2
import tensorflow as tf
%tensorflow_version 2.x
device_name = tf.test.gpu_device_name()
if device_name != '/device:GPU:0':
  raise SystemError('GPU device not found')
print('Found GPU at: {}'.format(device_name))

%config InlineBackend.figure_formats = {'png', 'retina'}

# from keras.optimizers import Adam, RMSprop, SGD


connection = pymysql.connect(
    host="localhost",
    db="gsacf_L05_15w",
    user="root",
    passwd="",
    charset="utf8",
    cursorclass=pymysql.cursors.DictCursor
)


sql = "SELECT * FROM python_table"
cursor = connection.cursor()
cursor.execute(sql)
python_table = cursor.fetchall()


for photo in python_table:

    posted_at_point = {photo["posted_at"]: photo["php_point"]}
    list_a = [posted_at_point]
    # print(list_a,end=",")
    # print( list_a)

# print("要素数は " + str(len(python_table)) + " です。")


imlist = []
for i in range(int(len(python_table))):
    imlist.append(python_table[i][("posted_at")])
print(imlist)

# 配列のURLを画像数値として配列
imtest1 = []
for i in range(int(len(imlist))):
    imtest1.append(imread(imlist[i]))
# print(imtest1)

# 画像数値として配列した値を画像化
imtest2 = []
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


# ラベルポイント
y = []
for i in range(int(len(python_table))):
    y.append(python_table[i][("php_point")])
# print(y)


X = np.array(X)
Y = np.array(y)


# trainデータとtestデータに分割
X_train, X_test, y_train, y_test = train_test_split(
    X,
    Y,
    random_state=0,
    test_size=0.2
)
# del X,
print(X_train.shape, y_train.shape, X_test.shape, y_test.shape)

# データ型の変換＆正規化
# データを float 型に変換
# 0〜255 までの範囲のデータを 0〜1 までの範囲に変更
X_train = X_train.astype('float32') / 255
X_test = X_test.astype('float32') / 255

# trainデータからvalidデータを分割
X_train, X_valid, y_train, y_valid = train_test_split(
    X_train,
    y_train,
    random_state=0,
    test_size=0.2
)
print(X_train.shape, y_train.shape, X_valid.shape, y_valid.shape)


base_model = Xception(
    include_top=False,
    weights="imagenet",
    input_shape=None
)


# 全結合層の新規構築
# 今回は分類ではなく、回帰のため、最後に予測値を１つ出力
# そのため出力層のユニット数を１つにし、活性化関数のsoftmax（確率値に変換）も使用しない


x = base_model.output
x = GlobalAveragePooling2D()(x)
x = Dense(1024, activation='relu')(x)
predictions = Dense(1)(x)


datagen = ImageDataGenerator(
    featurewise_center=False,
    samplewise_center=False,
    featurewise_std_normalization=False,
    samplewise_std_normalization=False,
    zca_whitening=False,
    rotation_range=0,
    width_shift_range=0.1,
    height_shift_range=0.1,
    horizontal_flip=True,
    vertical_flip=False
)


# EarlyStopping
early_stopping = EarlyStopping(
    monitor='val_loss',
    patience=10,
    verbose=1
)

# ModelCheckpoint
weights_dir = './weights/'
if os.path.exists(weights_dir) == False:
  os.mkdir(weights_dir)
model_checkpoint = ModelCheckpoint(
    weights_dir + "val_loss{val_loss:.3f}.hdf5",
    monitor='val_loss',
    verbose=1,
    save_best_only=True,
    save_weights_only=True,
    save_freq=3
)

# reduce learning rate
reduce_lr = ReduceLROnPlateau(
    monitor='val_loss',
    factor=0.1,
    patience=3,
    verbose=1
)

# log for TensorBoard
logging = TensorBoard(log_dir="log/")


# RMSE


def root_mean_squared_error(y_true, y_pred):
    return K.sqrt(K.mean(K.square(y_pred - y_true), axis=-1))


# XceptionをFine-tuning

# ネットワーク定義
model = Model(inputs=base_model.input, outputs=predictions)

#108層までfreeze
for layer in model.layers[:108]:
    layer.trainable = False

    # Batch Normalizationのfreeze解除
    if layer.name.startswith('batch_normalization'):
        layer.trainable = True
    if layer.name.endswith('bn'):
        layer.trainable = True

#109層以降、学習させる
for layer in model.layers[108:]:
    layer.trainable = True

# layer.trainableの設定後にcompile
model.compile(
    optimizer=adam_v2.Adam(),
    loss=root_mean_squared_error,
)


% % time
hist = model.fit(
    datagen.flow(X_train, y_train, batch_size=32),
    steps_per_epoch=X_train.shape[0] // 32,
    epochs=100,
    validation_data=(X_valid, y_valid),
    callbacks=[early_stopping, reduce_lr],
    shuffle=True,
    verbose=1
)


# 学習曲線のプロット
plt.figure(figsize=(18, 6))

# loss
plt.subplot(1, 2, 1)
plt.plot(hist.history["loss"], label="loss", marker="o")
plt.plot(hist.history["val_loss"], label="val_loss", marker="o")
#plt.yticks(np.arange())
#plt.xticks(np.arange())
plt.ylabel("loss")
plt.xlabel("epoch")
plt.title("")
plt.legend(loc="best")
plt.grid(color='gray', alpha=0.2)

plt.show()


score = model.evaluate(X_test, y_test, verbose=1)
print("evaluate loss: {}".format(score))


# モデルの保存
model_dir = '/'
if os.path.exists(model_dir) == False:
  os.mkdir(model_dir)


# optimizerのない軽量モデルを保存（学習や評価不可だが、予測は可能）
model.save(model_dir + 'model-opt.hdf5', include_optimizer=False)


cursor.close()
connection.close()
