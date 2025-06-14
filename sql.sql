-- Create ENUM types
CREATE TYPE user_role AS ENUM ('admin', 'enseignant');

CREATE TYPE material_status AS ENUM ('new', 'used', 'broken-down');

-- Create users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role user_role NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMPTZ NULL
);

-- Create materials table
CREATE TABLE materials (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    quantity INT DEFAULT 1,
    status material_status DEFAULT 'new',
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

-- Create reservations table
CREATE TABLE reservations (
    id SERIAL PRIMARY KEY,
    start_date TIMESTAMPTZ NOT NULL,
    end_date TIMESTAMPTZ NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Create reservation_material pivot table
CREATE TABLE reservation_material (
    reservation_id INT NOT NULL,
    material_id INT NOT NULL,
    PRIMARY KEY (reservation_id, material_id),
    FOREIGN KEY (reservation_id) REFERENCES reservations (id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials (id) ON DELETE CASCADE
);

-- Create password_resets table
CREATE TABLE password_resets (
    id SERIAL PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expire_at TIMESTAMPTZ NOT NULL DEFAULT(
        CURRENT_TIMESTAMP + INTERVAL '1 hour'
    ),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX idx_token ON password_resets (token);

CREATE INDEX idx_email_resets ON password_resets (email);

CREATE INDEX idx_email_users ON users (email);

-- Seed data
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
    ),
    (
        'teacher',
        'teacher@test.com',
        '$2y$10$7V1ay2szEAHqEsOKahcFlOtnyKCualwLm72y.suzoLer5hJGU4oIG',
        'enseignant'
    );