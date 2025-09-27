INSERT INTO books (
  title, author, category, year, description, cover_url, status, quantity,
  price_buy, price_rent_2w, price_rent_1m, price_rent_3m
) VALUES
('Чистый код',
 'Роберт Мартин',
 'Программирование',
 2008,
 'Руководство по профессиональной разработке программного обеспечения',
 'https://images-na.ssl-images-amazon.com/images/I/41xShlnTZTL._SX374_BO1,204,203,200_.jpg',
 'available', 5, 120.00, 20.00, 30.00, 60.00),

('Прагматичный программист',
 'Эндрю Хант; Дэвид Томас',
 'Программирование',
 1999,
 'Путь к мастерству',
 NULL,
 'available', 3, 110.00, 18.00, 28.00, 55.00),

('Преступление и наказание',
 'Фёдор Достоевский',
 'Классика',
 1866,
 'Психологический роман',
 NULL,
 'available', 2, 50.00, 10.00, 15.00, 30.00);

INSERT INTO users(name,email,password_hash,role)
 VALUES ('Admin','admin@bookstore.local', crypt('admin123', gen_salt('bf')), 'admin')
 ON CONFLICT DO NOTHING;
