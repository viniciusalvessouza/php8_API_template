CREATE DATABASE apiphp8

USE apiphp8

CREATE TABLE IF NOT EXISTS usuario(
    ID_USUARIO int auto_increment primary key,
    name varchar(20) NOT NULL
    rg varchar(50) NOT NULL,
    email varchar(30) NOT NULL UNIQUE,
    ocupacao int NOT NULL,
    password varchar(500) NOT NULL,
    IDToken varchar(500) NOT NULL,
    data varchar(17) NOT NULL
)