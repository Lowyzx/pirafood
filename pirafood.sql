CREATE DATABASE pirafood;

USE pirafood;
CREATE TABLE cliente(
    id int AUTO_INCREMENT primary key,
    nome VARCHAR(100),
    email VARCHAR(100),
    senha VARCHAR(255),
    cpf CHAR(14)
);
CREATE TABLE pratos(
    id int AUTO_INCREMENT PRIMARY KEY,
    nome_prato VARCHAR(100),
    descricao VARCHAR(255),
    preco DOUBLE(10,7)
);
select * from cliente;