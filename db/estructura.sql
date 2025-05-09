-- Crear base de datos
CREATE DATABASE IF NOT EXISTS shining_festival;
USE shining_festival;

-- Tabla de usuarios (admin y jurados)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'jurado') NOT NULL,
    area ENUM('musica', 'ingles') DEFAULT NULL, -- Solo aplica para jurados
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de participantes
CREATE TABLE participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria ENUM('Kids', 'Teens', 'Seniors') NOT NULL,
    modalidad ENUM('Solista', 'Grupo') NOT NULL,
    video_url VARCHAR(255),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de calificaciones
CREATE TABLE calificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participante_id INT NOT NULL,
    jurado_id INT NOT NULL,
    
    -- Rúbrica: Inglés
    pronunciacion TINYINT CHECK (pronunciacion BETWEEN 1 AND 10),
    fluidez TINYINT CHECK (fluidez BETWEEN 1 AND 10),
    vocabulario TINYINT CHECK (vocabulario BETWEEN 1 AND 10),
    
    -- Rúbrica: Musical
    afinacion TINYINT CHECK (afinacion BETWEEN 1 AND 10),
    proyeccion_vocal TINYINT CHECK (proyeccion_vocal BETWEEN 1 AND 10),
    interpretacion TINYINT CHECK (interpretacion BETWEEN 1 AND 10),
    
    -- Rúbrica: Visual
    story_time TINYINT CHECK (story_time BETWEEN 1 AND 10),
    diseno_escenico TINYINT CHECK (diseno_escenico BETWEEN 1 AND 10),
    
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Relaciones y restricciones
    UNIQUE (participante_id, jurado_id), -- Solo una calificación por jurado
    FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE,
    FOREIGN KEY (jurado_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
