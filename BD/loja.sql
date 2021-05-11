drop database if exists loja;
create database if not exists loja CHARACTER SET utf8 COLLATE utf8_general_ci;
use loja;

create table if not exists usuario(
    id int primary key auto_increment,
    nome varchar(200) not null,
    email varchar(100) unique,
    senha varchar(253) not null
) DEFAULT  CHARSET = utf8;