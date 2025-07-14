
CREATE DATABASE IF NOT EXISTS mindcare;
USE mindcare;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user'
);

CREATE TABLE moods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  mood VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE journals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  entry TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE chat_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  message TEXT,
  role ENUM('user','bot'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quality_scores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  functionality INT,
  usability INT,
  reliability INT,
  efficiency INT,
  security INT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (email, password, role) VALUES ('admin@mindcare.com', 'admin123', 'admin');
