CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'enseignant') NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL
);

CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    quantity INT DEFAULT 1,
    status ENUM('new', 'used', 'broken-down') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE reservation_material (
    reservation_id INT NOT NULL,
    material_id INT NOT NULL,
    PRIMARY KEY (reservation_id, material_id),
    FOREIGN KEY (reservation_id) REFERENCES reservations (id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials (id) ON DELETE CASCADE
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expire_at DATETIME NOT NULL DEFAULT(
        CURRENT_TIMESTAMP + INTERVAL 1 HOUR
    ),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_token ON password_resets (token);

CREATE INDEX idx_email ON password_resets (email);

CREATE INDEX idx_email ON users (email);

INSERT INTO
    users (
        username,
        email,
        password,
        role
    )
VALUES (
        'admin',
        'admin@test.com',
        '$2y$10$mm1P5EKUMM1mpyhDR9k2ru3KvVDsacLA8ELy.O/IxZ3DCB1UL3sXe',
        'admin'
    );

INSERT INTO
    users (
        username,
        email,
        password,
        role
    )
VALUES (
        'teacher',
        'teacher@test.com',
        '$2y$10$7V1ay2szEAHqEsOKahcFlOtnyKCualwLm72y.suzoLer5hJGU4oIG',
        'enseignant'
    );