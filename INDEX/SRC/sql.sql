-- -----------------------------------------------------
-- Schema PI22024
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `PI22024`;

CREATE SCHEMA IF NOT EXISTS `PI22024`;

USE `PI22024`;

-- -----------------------------------------------------
-- Table `PI22024`.`EMPRESA`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`EMPRESA` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `USERNAME` VARCHAR(65) NOT NULL,
    `TELEFONE` VARCHAR(18) NOT NULL,
    `CNPJ` VARCHAR(20) NOT NULL,
    `EMAIL` VARCHAR(256) NOT NULL,
    `SENHA` VARCHAR(200) NOT NULL,
    `PAGAMENTO` DATE NOT NULL DEFAULT(now() + interval 30 day),
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `EMP_NOME_UNIQUE` (`USERNAME` ASC),
    UNIQUE INDEX `EMP_TELEFONE_UNIQUE` (`TELEFONE` ASC),
    UNIQUE INDEX `EMP_CNPJ_UNIQUE` (`CNPJ` ASC),
    UNIQUE INDEX `EMP_EMAIL_UNIQUE` (`EMAIL` ASC)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`CARDAPIO`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`CARDAPIO` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `EMPRESA_ID` INT NOT NULL,
    `NOME` VARCHAR(45) NOT NULL,
    `DESCRICAO` TEXT NULL DEFAULT NULL,
    `INGREDIENTES` JSON NULL,
    `ADICIONAIS` JSON NULL,
    `PRECO` DOUBLE NOT NULL,
    `IMG` VARCHAR(500) NOT NULL DEFAULT './FILES/CARDAPIO/DEFAULT.png',
    `CATEGORIA` VARCHAR(45) NOT NULL,
    `STATUS` TINYINT NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_CARDAPIO_EMPRESA1_idx` (`EMPRESA_ID` ASC),
    CONSTRAINT `fk_CARDAPIO_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`MESA`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`MESA` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `EMPRESA_ID` INT NOT NULL,
    `NUMERO` INT NOT NULL,
    `TOKEN` VARCHAR(45) NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_MESA_EMPRESA1_idx` (`EMPRESA_ID` ASC),
    CONSTRAINT `fk_MESA_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`COMANDA`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`COMANDA` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `MESA_ID` INT NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_COMANDA_MESA1_idx` (`MESA_ID` ASC),
    CONSTRAINT `fk_COMANDA_MESA1` FOREIGN KEY (`MESA_ID`) REFERENCES `PI22024`.`MESA` (`ID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`HISTORICO`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`HISTORICO` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `EMPRESA_ID` INT NOT NULL,
    `DATA` DATE NOT NULL,
    `HISTORICO` LONGTEXT NOT NULL,
    PRIMARY KEY (`ID`, `EMPRESA_ID`),
    INDEX `fk_HISTORICO_EMPRESA1_idx` (`EMPRESA_ID` ASC),
    CONSTRAINT `fk_HISTORICO_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`PEDIDO`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`PEDIDO` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `COMANDA_ID` INT NOT NULL,
    `STATUS` ENUM(
        'COZINHA',
        'GARCOM',
        'CAIXA',
        'DESATIVADO'
    ) NOT NULL DEFAULT 'COZINHA',
    `DATA` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_PEDIDO_COMANDA1_idx` (`COMANDA_ID` ASC),
    CONSTRAINT `fk_PEDIDO_COMANDA1` FOREIGN KEY (`COMANDA_ID`) REFERENCES `PI22024`.`COMANDA` (`ID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`ITENS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`ITENS` (
    `ID` INT NOT NULL,
    `PEDIDO_ID` INT NOT NULL,
    `CARDAPIO_ID` INT NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    `QUANTIDADE` TEXT NOT NULL,
    `PRECO` DOUBLE NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_ITENS_PEDIDO1_idx` (`PEDIDO_ID` ASC),
    INDEX `fk_ITENS_CARDAPIO1_idx` (`CARDAPIO_ID` ASC),
    CONSTRAINT `fk_ITENS_CARDAPIO1` FOREIGN KEY (`CARDAPIO_ID`) REFERENCES `PI22024`.`CARDAPIO` (`ID`),
    CONSTRAINT `fk_ITENS_PEDIDO1` FOREIGN KEY (`PEDIDO_ID`) REFERENCES `PI22024`.`PEDIDO` (`ID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`USER`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`USER` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `EMPRESA_ID` INT NOT NULL,
    `USERNAME` VARCHAR(75) NOT NULL,
    `SENHA` VARCHAR(200) NOT NULL,
    `FUNCAO` ENUM(
        'COZINHA',
        'GARCOM',
        'CAIXA',
        'ADM'
    ) NOT NULL,
    `TOKEN` TEXT NOT NULL,
    `KEY` INT NOT NULL,
    `VALIDO` TINYINT NOT NULL DEFAULT '0',
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `GAR_NOME_UNIQUE` (`USERNAME` ASC),
    INDEX `fk_USER_EMPRESA1_idx` (`EMPRESA_ID` ASC),
    CONSTRAINT `fk_USER_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`)
) ENGINE = InnoDB;

-- Inserts para teste
-- Inserir dados na tabela EMPRESA
INSERT INTO `PI22024`.`EMPRESA` (`ID`, `USERNAME`, `TELEFONE`, `CNPJ`, `EMAIL`, `SENHA`, `PAGAMENTO`)
VALUES (1, 'TESTE', '(12) 9 9200-3001', '11.111.111/1111-12', 'teste@gmail.com', '$2y$10$RoqTKkKdGqu2rlynQHCOt.kQylGxE2eJcYdZoWfn5IUwMPFVWNJRa', '2024-11-16');

-- Inserir dados na tabela USER (ADM, GARCOM, COZINHA, CAIXA)
INSERT INTO `PI22024`.`USER` (`ID`, `EMPRESA_ID`, `USERNAME`, `SENHA`, `FUNCAO`, `TOKEN`, `KEY`, `VALIDO`)
VALUES 
(1, 1, 'teste', '$2y$10$RoqTKkKdGqu2rlynQHCOt.kQylGxE2eJcYdZoWfn5IUwMPFVWNJRa', 'ADM', 'f503d389d10898a34544eb1aa7bf6920de018dd6', 114885318, 1),
(2, 1, 'testeGARCOM', '$2y$10$RoqTKkKdGqu2rlynQHCOt.kQylGxE2eJcYdZoWfn5IUwMPFVWNJRa', 'GARCOM', 'd912a6f5c4b8392e2d3c9db8f1a5f0c7d4e5b60a', 731529834, 1),
(3, 1, 'testeCOZINHA', '$2y$10$RoqTKkKdGqu2rlynQHCOt.kQylGxE2eJcYdZoWfn5IUwMPFVWNJRa', 'COZINHA', 'a4d529b9fae18d347e935ba3f9c7b6b8f3f7e5c2', 582194657, 1),
(4, 1, 'testeCAIXA', '$2y$10$RoqTKkKdGqu2rlynQHCOt.kQylGxE2eJcYdZoWfn5IUwMPFVWNJRa', 'CAIXA', 'b703f6a7b2f1c834fa9e7bd4d0c9a0e6f7b8c3d9', 364829105, 1);


-- Inserir dados na tabela CARDAPIO
INSERT INTO `PI22024`.`CARDAPIO` (`EMPRESA_ID`, `NOME`, `DESCRICAO`, `INGREDIENTES`, `ADICIONAIS`, `PRECO`, `CATEGORIA`, `STATUS`)
VALUES 
(1, 'Hamburguer Simples', 'Hamburguer com pão, carne e queijo', 
    '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Carne", "alergicos": []}, {"nome": "Queijo", "alergicos": ["lactose"]}]', 
    '[{"nome": "Bacon", "alergicos": []}, {"nome": "Ovo", "alergicos": []}]', 
    15.99, 'Lanche', 1),
(1, 'Salada Caesar', 'Salada com alface, frango e molho caesar', 
    '[{"nome": "Alface", "alergicos": []}, {"nome": "Frango", "alergicos": []}, {"nome": "Molho Caesar", "alergicos": ["lactose"]}]', 
    '[{"nome": "Croutons", "alergicos": ["gluten"]}, {"nome": "Parmesão", "alergicos": ["lactose"]}]', 
    19.99, 'Salada', 1),
(1, 'Pizza Marguerita', 'Pizza de queijo e tomate', 
    '[{"nome": "Massa", "alergicos": ["gluten"]}, {"nome": "Queijo", "alergicos": ["lactose"]}, {"nome": "Tomate", "alergicos": []}]', 
    '[{"nome": "Azeitona", "alergicos": []}, {"nome": "Orégano", "alergicos": []}]', 
    29.99, 'Pizza', 1);

-- Inserir dados na tabela MESA
INSERT INTO `PI22024`.`MESA` (`EMPRESA_ID`, `NUMERO`, `TOKEN`)
VALUES 
(1, 1, 'TOKEN_MESA_1'),
(1, 2, 'TOKEN_MESA_2'),
(1, 3, 'TOKEN_MESA_3');

-- Inserir dados na tabela COMANDA
INSERT INTO `PI22024`.`COMANDA` (`MESA_ID`)
VALUES 
(1),
(2),
(3);

-- Inserir dados na tabela HISTORICO
INSERT INTO `PI22024`.`HISTORICO` (`EMPRESA_ID`, `DATA`, `HISTORICO`)
VALUES 
(1, CURDATE(), 'Histórico de vendas do dia 1'),
(1, CURDATE() - INTERVAL 1 DAY, 'Histórico de vendas do dia 2'),
(1, CURDATE() - INTERVAL 2 DAY, 'Histórico de vendas do dia 3');

-- Inserir dados na tabela PEDIDO
INSERT INTO `PI22024`.`PEDIDO` (`COMANDA_ID`, `STATUS`, `DATA`)
VALUES 
(1, 'COZINHA', NOW()),
(2, 'GARCOM', NOW()),
(3, 'COZINHA', NOW());

-- Inserir dados na tabela ITENS
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(1, 1, 1, 'Sem Maiosnese', 15.99, 1),
(2, 2, 2, 'Caprichada no tempero', 19.99, 2),
(3, 3, 3, 'Borda recheada', 29.99, 3);