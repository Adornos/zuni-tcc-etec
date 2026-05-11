CREATE DATABASE db_zuni;

USE db_zuni;

CREATE TABLE serie (
    id_serie INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50)
);

INSERT INTO serie (nome) VALUES
('1º Fundamental'),
('2º Fundamental'),
('3º Fundamental');

CREATE TABLE solicitacao_matricula (
    id_solicitacao INT AUTO_INCREMENT PRIMARY KEY,

    nome_aluno VARCHAR(100) NOT NULL,
    data_nascimento DATE,
    sexo_aluno ENUM('M','F','O'),

    id_serie INT,

    logradouro VARCHAR(100),
    numero VARCHAR(10),
    bairro VARCHAR(50),
    cidade VARCHAR(50),
    estado INT,

    neurodivergencia BOOLEAN,
    alergia BOOLEAN,
    restricao_alimentar BOOLEAN,
    cuidados_especiais BOOLEAN,
    descricao TEXT,

    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_serie) REFERENCES serie(id_serie)
);


CREATE TABLE solicitacao_responsavel (

    id_responsavel INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
	 email VARCHAR(100),
    telefone VARCHAR(20),
    cpf VARCHAR(20) UNIQUE,
    sexo ENUM('M','F','O')
    
);

CREATE TABLE relacao_responsavel_matricula (
    id_solicitacao INT,
    id_responsavel INT,
    parentesco VARCHAR(50),

    PRIMARY KEY (id_solicitacao, id_responsavel),

    FOREIGN KEY (id_solicitacao) REFERENCES solicitacao_matricula(id_solicitacao),
    FOREIGN KEY (id_responsavel) REFERENCES solicitacao_responsavel(id_responsavel)
);
