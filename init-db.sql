-- Создание таблиц
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS authors (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    biography TEXT
);

CREATE TABLE IF NOT EXISTS books (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author_id INTEGER REFERENCES authors(id),
    category_id INTEGER REFERENCES categories(id),
    publication_year INTEGER,
    price DECIMAL(10,2) NOT NULL,
    rental_price_week DECIMAL(10,2),
    rental_price_month DECIMAL(10,2),
    rental_price_3months DECIMAL(10,2),
    is_available BOOLEAN DEFAULT TRUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    book_id INTEGER REFERENCES books(id),
    type VARCHAR(20) NOT NULL,
    rental_period VARCHAR(20),
    amount DECIMAL(10,2) NOT NULL,
    start_date DATE,
    end_date DATE,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Вставка тестовых данных
INSERT INTO categories (name, description) VALUES
('Фантастика', 'Научная фантастика и фэнтези'),
('Детективы', 'Детективные романы и триллеры'),
('Романы', 'Художественная литература'),
('Программирование', 'Техническая литература по программированию');

INSERT INTO authors (name, biography) VALUES
('Айзек Азимов', 'Американский писатель-фантаст'),
('Дэн Браун', 'Американский писатель, автор детективов'),
('Лев Толстой', 'Русский писатель и мыслитель'),
('Роберт Мартин', 'Американский инженер программного обеспечения');

INSERT INTO books (title, author_id, category_id, publication_year, price, rental_price_week, rental_price_month, rental_price_3months, description) VALUES
('Основание', 1, 1, 1951, 500, 50, 150, 350, 'Классика научной фантастики'),
('Код да Винчи', 2, 2, 2003, 450, 45, 120, 300, 'Захватывающий детектив'),
('Война и мир', 3, 3, 1869, 600, 60, 180, 450, 'Великий русский роман'),
('Чистый код', 4, 4, 2008, 800, 80, 200, 500, 'Руководство по написанию качественного кода');

INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bookstore.ru', 'admin'),
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user1@bookstore.ru', 'user');
