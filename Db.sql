CREATE DATABASE YandexMusic;

USE YandexMusic;

CREATE TABLE artists (
     id INT AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(255) NOT NULL UNIQUE,
     subscribers INT DEFAULT 0,
     listeners INT DEFAULT 0,
     albums INT DEFAULT 0
);

CREATE TABLE music (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    duration INT DEFAULT 0,
    artist_id INT NOT NULL,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
);