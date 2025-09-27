CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  name TEXT NOT NULL,
  email TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'user', -- 'user' | 'admin'
  created_at TIMESTAMP NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS books (
  id SERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  author TEXT NOT NULL,
  category TEXT NOT NULL,
  year INTEGER NOT NULL,
  description TEXT,
  cover_url TEXT,
  status TEXT NOT NULL DEFAULT 'available', -- available | unavailable | archived
  quantity INTEGER NOT NULL DEFAULT 1,
  price_buy NUMERIC(10,2) NOT NULL DEFAULT 0,
  price_rent_2w NUMERIC(10,2) NOT NULL DEFAULT 0,
  price_rent_1m NUMERIC(10,2) NOT NULL DEFAULT 0,
  price_rent_3m NUMERIC(10,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS transactions (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  book_id INTEGER NOT NULL REFERENCES books(id) ON DELETE CASCADE,
  type TEXT NOT NULL, -- 'buy' | 'rent'
  start_date DATE NOT NULL DEFAULT CURRENT_DATE,
  due_date DATE, -- только для аренды
  price_paid NUMERIC(10,2) NOT NULL,
  status TEXT NOT NULL DEFAULT 'active', -- active | returned | completed | canceled
  created_at TIMESTAMP NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS email_queue (
  id SERIAL PRIMARY KEY,
  to_email TEXT NOT NULL,
  subject TEXT NOT NULL,
  body TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'queued', -- queued | sent | failed
  created_at TIMESTAMP NOT NULL DEFAULT now(),
  sent_at TIMESTAMP
);

-- Админ
INSERT INTO users(name, email, password_hash, role)
VALUES ('Admin', 'admin@bookstore.local', crypt('admin123', gen_salt('bf')), 'admin')
ON CONFLICT DO NOTHING;

-- Любимые книги (пример)
INSERT INTO books (title, author, category, year, description, cover_url, status, quantity,
                   price_buy, price_rent_2w, price_rent_1m, price_rent_3m)
VALUES
('Clean Code', 'Robert C. Martin', 'Programming', 2008, 'A Handbook of Agile Software Craftsmanship',
 'https://images-na.ssl-images-amazon.com/images/I/41xShlnTZTL._SX374_BO1,204,203,200_.jpg',
 'available', 5, 120.00, 20.00, 30.00, 60.00),
('The Pragmatic Programmer', 'Andrew Hunt; David Thomas', 'Programming', 1999,
 'Journey to Mastery', NULL, 'available', 3, 110.00, 18.00, 28.00, 55.00),
('Crime and Punishment', 'Fyodor Dostoevsky', 'Classics', 1866, 'A psychological novel', NULL,
 'available', 2, 50.00, 10.00, 15.00, 30.00);
