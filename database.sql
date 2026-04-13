CREATE DATABASE IF NOT EXISTS web_technologies_lab;
USE web_technologies_lab;

CREATE TABLE IF NOT EXISTS exp9_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS exp10_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    department VARCHAR(120) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO exp9_products (title, category, price)
SELECT * FROM (
    SELECT 'Web Development Book', 'Books', 350.00
    UNION ALL
    SELECT 'USB Keyboard', 'Electronics', 650.00
    UNION ALL
    SELECT 'Scientific Calculator', 'Accessories', 720.00
) AS seed_products
WHERE NOT EXISTS (SELECT 1 FROM exp9_products);

INSERT INTO exp10_users (name, email, department, password)
SELECT * FROM (
    SELECT 'Demo Student', 'student@demo.com', 'Computer Science', '$2y$10$e28HRpCbbFIehtIUSunvmeYpsU5HHaRib7Mzq3RElBlvzLKPD5yQu'
) AS seed_user
WHERE NOT EXISTS (SELECT 1 FROM exp10_users WHERE email = 'student@demo.com');
