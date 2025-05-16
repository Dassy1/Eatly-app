-- Add Akara recipe
INSERT INTO recipes (title, image_url, source_url, publisher, cooking_time, servings, user_id) VALUES
('Akara (Bean Cakes)', 'https://www.eatingnigeria.com/wp-content/uploads/2023/03/Akara-Bean-Cakes.jpg', 'https://www.eatingnigeria.com/akara-bean-cakes/', 'Nigerian Cuisine', 30, 6, 1);

-- Get the ID of the newly inserted recipe
SET @akara_id = LAST_INSERT_ID();

-- Add Akara ingredients
INSERT INTO ingredients (recipe_id, quantity, unit, description) VALUES
(@akara_id, 1, 'kg', 'black-eyed peas'),
(@akara_id, 1, 'cup', 'onions'),
(@akara_id, 1, 'tsp', 'salt'),
(@akara_id, 1, 'tsp', 'pepper'),
(@akara_id, 1, 'tsp', 'thyme'),
(@akara_id, 1, 'cup', 'palm oil');
