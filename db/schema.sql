-- 1. initialize database
CREATE DATABASE IF NOT EXISTS bubbletea_db;
USE bubbletea_db;

-- 2. create category table (for navbar: Milk Tea, Fruit Tea, Coffee)
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE
);

-- 3. create product table (store name, price, image path)
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),  -- store image path
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- 4. create order summary table (record who placed the order, how much was paid, Stripe status)
CREATE TABLE IF NOT EXISTS orders (
    order_id VARCHAR(100) PRIMARY KEY, -- use the session ID returned by Stripe
    customer_phone_no VARCHAR(100),
    total_amount DECIMAL(10, 2),
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. create order items table (each row represents a cup of milk tea, including specific specifications)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100),
    product_name VARCHAR(100), -- duplicate the name to prevent confusion 
                               -- if the product table is modified later
    size VARCHAR(20),          -- size of bubble tea - Regular / Large
    sugar_level VARCHAR(20),    -- sugar level - half sugar / full sugar / etc
    ice_level VARCHAR(20),      -- ice level - no ice / normal
    quantity INT DEFAULT 1,
    subtotal DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- 6. preload some demo data
INSERT INTO categories (category_name) VALUES ('Bubble Tea'), ('Fruit Tea'), ('Coffee');

-- Assuming we selected the Milk Tea category (ID is 1)
INSERT INTO products (category_id, name, price, image_url, description) VALUES 
(1, 'Classic Pearl Milk Tea', 6.50, 'images/pearl_tea.jpg', 'Q弹珍珠，醇厚奶香'),
(1, 'Bobo Caramel Milk', 7.00, 'images/bobo_tea.jpg', '焦糖风味，口感丰富'),
(2, 'Multi-layered Grape', 8.50, 'images/grape_tea.jpg', '新鲜葡萄，清爽芝士');
