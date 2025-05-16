-- EATLY Database Schema

-- Drop tables if they exist
DROP TABLE IF EXISTS bookmarks;
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS recipes;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create recipes table
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    source_url VARCHAR(255),
    publisher VARCHAR(100),
    cooking_time INT,
    servings INT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create ingredients table
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    quantity DECIMAL(10,2),
    unit VARCHAR(50),
    description VARCHAR(255) NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- Create bookmarks table
CREATE TABLE bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, recipe_id)
);

-- Insert sample data (optional)
-- Sample users (password is 'password123' hashed with password_hash)
INSERT INTO users (username, email, password) VALUES
('dassy01', 'dassy@gmail.com', '1234567890'),
('jane_smith', 'jane@outlook.com', 'yyyyyyyy');

-- Sample recipes
INSERT INTO recipes (title, image_url, source_url, publisher, cooking_time, servings, user_id) VALUES
('Spaghetti Carbonara', 'https://static01.nyt.com/images/2021/02/14/dining/carbonara-horizontal/carbonara-horizontal-master768-v2.jpg?quality=75&auto=webp', 'https://cooking.nytimes.com/recipes/12965-spaghetti-carbonara', 'Italian Cuisine', 30, 4, 1),
('Chicken Tikka Masala', 'https://www.jocooks.com/wp-content/uploads/2024/01/chicken-tikka-masala-1-26-730x913.jpg', 'https://www.jocooks.com/recipes/chicken-tikka-masala/', 'Indian Delights', 45, 6, 1),
('Vegetable Stir Fry', 'https://i0.wp.com/kristineskitchenblog.com/wp-content/uploads/2024/01/vegetable-stir-fry-22-2.jpg?resize=700%2C1050&ssl=1', 'https://kristineskitchenblog.com/vegetable-stir-fry/', 'Healthy Eats', 20, 2, 2);

-- Sample ingredients
INSERT INTO ingredients (recipe_id, quantity, unit, description) VALUES
(1, 400, 'g', 'spaghetti'),
(1, 200, 'g', 'pancetta'),


-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create payment_methods table
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    card_number VARCHAR(255) NULL,
    expiry_month VARCHAR(2) NULL,
    expiry_year VARCHAR(4) NULL,
    name_on_card VARCHAR(100) NULL,
    paypal_email VARCHAR(255) NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes for better performance
CREATE INDEX idx_payments_user_id ON payments(user_id);
CREATE INDEX idx_payment_methods_user_id ON payment_methods(user_id);
(1, 3, NULL, 'large eggs'),
(1, 50, 'g', 'pecorino cheese'),
(1, 50, 'g', 'parmesan'),
(1, NULL, NULL, 'black pepper'),
(2, 500, 'g', 'chicken breast'),
(2, 1, NULL, 'large onion'),
(2, 3, NULL, 'garlic cloves'),
(2, 400, 'g', 'chopped tomatoes'),
(2, 100, 'ml', 'yogurt'),
(2, NULL, NULL, 'spices'),
(3, 1, NULL, 'bell pepper'),
(3, 1, NULL, 'carrot'),
(3, 100, 'g', 'broccoli'),
(3, 2, 'tbsp', 'soy sauce');

-- Sample bookmarks
INSERT INTO bookmarks (user_id, recipe_id) VALUES
(1, 2),
(2, 1);
