import tensorflow as tf
from tensorflow.keras.applications import InceptionV3
from tensorflow.keras.layers import Dense, GlobalAveragePooling2D, Dropout
from tensorflow.keras.models import Model
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.optimizers import Adam

# Load InceptionV3 (without top layers)
base_model = InceptionV3(weights='imagenet', include_top=False)

# Add custom classification layers
x = base_model.output
x = GlobalAveragePooling2D()(x)
x = Dense(1024, activation='relu')(x)
x = Dropout(0.5)(x)  # Adjusted Dropout
predictions = Dense(3, activation='softmax')(x)  

# Create the model
model = Model(inputs=base_model.input, outputs=predictions)

# Freeze all layers except the top 50
for layer in base_model.layers[:-50]:  
    layer.trainable = False

# Compile with a lower learning rate
model.compile(optimizer=Adam(learning_rate=0.0001), loss='categorical_crossentropy', metrics=['accuracy'])

# Data augmentation for better generalization
train_datagen = ImageDataGenerator(
    rescale=1.0/255, rotation_range=30, width_shift_range=0.2,
    height_shift_range=0.2, zoom_range=0.3, shear_range=0.2,
    horizontal_flip=True, brightness_range=[0.8, 1.2],
    validation_split=0.2  
)

# Load training data
train_generator = train_datagen.flow_from_directory(
    'dataset', target_size=(299, 299), batch_size=32, class_mode='categorical', subset='training'
)

# Load validation data
val_generator = train_datagen.flow_from_directory(
    'dataset', target_size=(299, 299), batch_size=32, class_mode='categorical', subset='validation'
)

# Train the model
model.fit(train_generator, validation_data=val_generator, epochs=22, steps_per_epoch=len(train_generator))

# Save the trained model
model.save('model/tea_leaf_model.h5')
