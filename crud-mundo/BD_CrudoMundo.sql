CREATE DATABASE Crud_Mundo;
USE Crud_Mundo;

CREATE TABLE Continente (
    id_Continente INT AUTO_INCREMENT PRIMARY KEY,
    nome_Continente VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Pais (
    id_Pais INT AUTO_INCREMENT PRIMARY KEY,
    nome_Pais VARCHAR(100) NOT NULL UNIQUE,
    populacao_Pais FLOAT NOT NULL,
    idioma_Pais VARCHAR(100) NOT NULL,
    id_Continente INT NOT NULL,
    FOREIGN KEY (id_Continente) REFERENCES Continente(id_Continente)
);

CREATE TABLE Cidade (
    id_Cidade INT AUTO_INCREMENT PRIMARY KEY,
    nome_Cidade VARCHAR(100) NOT NULL,
    populacao_Cidade BIGINT NOT NULL, 
    id_Pais INT NOT NULL,
    FOREIGN KEY (id_Pais) REFERENCES Pais(id_Pais)
);

INSERT INTO Continente (nome_Continente) VALUES
('América'),
('África'),
('Ásia'),
('Europa'),
('Oceania'),
('Antártida');

ALTER TABLE Pais
ADD COLUMN capital_Pais VARCHAR(100) NULL,
ADD COLUMN moeda_Pais VARCHAR(100) NULL,
ADD COLUMN bandeira_Pais_url VARCHAR(255) NULL;
