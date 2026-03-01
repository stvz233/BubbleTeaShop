-- 1. 初始化数据库
CREATE DATABASE IF NOT EXISTS bubble_tea_db;
USE bubble_tea_db;

-- 2. 创建产品品类表 (用于导航栏：奶茶、果茶、咖啡)
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE
);

-- 3. 创建产品表 (存储名称、价格、图片路径)
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),  -- 存放图片路径
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- 4. 创建订单汇总表 (记录是谁下的单，付了多少钱，Stripe 状态)
CREATE TABLE IF NOT EXISTS orders (
    order_id VARCHAR(100) PRIMARY KEY, -- 使用 Stripe 返回的 Session ID
    customer_phone_no VARCHAR(100),
    total_amount DECIMAL(10, 2),
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. 创建订单明细表 (每一行代表一杯奶茶，包含具体的规格)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100),
    product_name VARCHAR(100), -- 冗余存储名称，防止产品表修改后历史订单混乱
    size VARCHAR(20),          -- Regular / Large
    sugar_level VARCHAR(20),    -- 半糖 / 全糖等
    ice_level VARCHAR(20),      -- 去冰 / 正常等
    quantity INT DEFAULT 1,
    subtotal DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- 6. 预置一些演示数据
INSERT INTO categories (category_name) VALUES ('奶茶类'), ('果茶类'), ('咖啡类');

-- 假设导航栏选了奶茶类
INSERT INTO products (category_id, name, price, image_url, description) VALUES 
(1, '经典珍珠奶茶', 6.50, 'images/pearl_tea.jpg', 'Q弹珍珠，醇厚奶香'),
(1, '波波烤奶', 7.00, 'images/bobo_tea.jpg', '焦糖风味，口感丰富'),
(2, '多肉葡萄', 8.50, 'images/grape_tea.jpg', '新鲜葡萄，清爽芝士');
