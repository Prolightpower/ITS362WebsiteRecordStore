CREATE DATABASE IF NOT EXISTS RecordStore;
USE RecordStore;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password CHAR(64) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Albums table
CREATE TABLE IF NOT EXISTS albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    artist VARCHAR(100) NOT NULL,
    genre VARCHAR(50),
    year INT,
    price DECIMAL(6,2) NOT NULL,
    stock INT DEFAULT 0,
    description TEXT,
    cover_color VARCHAR(7) DEFAULT '#1a1a2e',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin account (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@recordstore.com', SHA2('admin123', 256), 'admin');

-- Insert sample albums
INSERT INTO albums (title, artist, genre, year, price, stock, description, cover_color) VALUES
('Kind of Blue', 'Miles Davis', 'Jazz', 1959, 24.99, 12, 'The best-selling jazz album of all time. A landmark in modal jazz.', '#1a3a5c'),
('Rumours', 'Fleetwood Mac', 'Rock', 1977, 22.99, 8, 'One of the best-selling albums of all time with timeless classics.', '#5c3a1a'),
('Purple Rain', 'Prince', 'Pop/Rock', 1984, 21.99, 15, 'The iconic soundtrack album that defined an era.', '#3a1a5c'),
('Abbey Road', 'The Beatles', 'Rock', 1969, 26.99, 6, 'The eleventh studio album and a masterpiece of pop music.', '#2a4a2a'),
('Thriller', 'Michael Jackson', 'Pop', 1982, 23.99, 20, 'The best-selling album of all time featuring seven hit singles.', '#4a2a2a'),
('Blue', 'Joni Mitchell', 'Folk', 1971, 20.99, 9, 'Universally regarded as one of the greatest albums ever made.', '#1a2a4a'),
('Nevermind', 'Nirvana', 'Grunge', 1991, 19.99, 11, 'The album that brought alternative rock to mainstream audiences.', '#2a3a2a'),
('Dark Side of the Moon', 'Pink Floyd', 'Progressive Rock', 1973, 27.99, 7, 'A concept album exploring themes of conflict, greed, and mental illness.', '#0a0a1a');
