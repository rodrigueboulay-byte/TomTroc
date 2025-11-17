-- Création de la base
CREATE DATABASE IF NOT EXISTS tomtroc
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tomtroc;

-- ============================
-- TABLE UTILISATEURS
-- ============================
CREATE TABLE user (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    city VARCHAR(100) NULL,
    bio TEXT NULL,
    avatar_path VARCHAR(255) NULL,
    roles VARCHAR(50) NOT NULL DEFAULT 'ROLE_USER', -- pour un éventuel admin
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================
-- TABLE GENRES
-- ============================
CREATE TABLE genre (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ============================
-- TABLE LIVRES
-- ============================
CREATE TABLE book (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL, -- propriétaire du livre
    genre_id INT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT NULL,
    book_condition ENUM('comme_neuf','tres_bon','bon','correct','abime') NOT NULL DEFAULT 'bon',
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    cover_image_path VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    CONSTRAINT fk_book_user
        FOREIGN KEY (user_id) REFERENCES user(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_book_genre
        FOREIGN KEY (genre_id) REFERENCES genre(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================
-- TABLE DEMANDES D’ÉCHANGE
-- ============================
CREATE TABLE exchange_request (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    requester_id INT UNSIGNED NOT NULL,       -- celui qui propose l’échange
    requested_id INT UNSIGNED NOT NULL,       -- propriétaire du livre demandé
    offered_book_id INT UNSIGNED NOT NULL,    -- livre offert
    requested_book_id INT UNSIGNED NOT NULL,  -- livre demandé
    status ENUM('pending','accepted','refused','cancelled','completed')
        NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    CONSTRAINT fk_exchange_requester
        FOREIGN KEY (requester_id) REFERENCES user(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_exchange_requested
        FOREIGN KEY (requested_id) REFERENCES user(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_exchange_offered_book
        FOREIGN KEY (offered_book_id) REFERENCES book(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_exchange_requested_book
        FOREIGN KEY (requested_book_id) REFERENCES book(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================
-- TABLE MESSAGES (MESSAGERIE)
-- ============================
CREATE TABLE message (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    exchange_id INT UNSIGNED NULL, -- optionnel : lier à une demande d’échange
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0,

    CONSTRAINT fk_message_sender
        FOREIGN KEY (sender_id) REFERENCES user(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_message_receiver
        FOREIGN KEY (receiver_id) REFERENCES user(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_message_exchange
        FOREIGN KEY (exchange_id) REFERENCES exchange_request(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================
-- QUELQUES GENRES PAR DÉFAUT
-- ============================
INSERT INTO genre (name) VALUES
('Roman'),
('Science-fiction'),
('Fantastique'),
('Policier'),
('Essai'),
('Biographie');
