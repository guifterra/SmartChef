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
    `PAGAMENTO` DATE NOT NULL,
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
    `IMG` VARCHAR(500) NOT NULL DEFAULT 'DEFAULT.png',
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
        'CAIXA'
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
    `USERNAME` VARCHAR(75) NOT NULL UNIQUE,
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
INSERT INTO `PI22024`.`CARDAPIO` (`EMPRESA_ID`, `NOME`, `DESCRICAO`, `INGREDIENTES`, `ADICIONAIS`, `PRECO`, `CATEGORIA`, `STATUS`, `IMG`)
VALUES 
(1, 'Hamburguer Simples', 'Hamburguer com pão, carne e queijo', 
    '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Carne", "alergicos": []}, {"nome": "Queijo", "alergicos": ["lactose"]}]', 
    '[{"nome": "Bacon", "alergicos": [], "preco": 2.50}, {"nome": "Ovo", "alergicos": [], "preco": 1.50}]', 
    15.99, 'Lanche', 1, 'hamburguer_simples.png'),
    
(1, 'Salada Caesar', 'Salada com alface, frango e molho caesar', 
    '[{"nome": "Alface", "alergicos": []}, {"nome": "Frango", "alergicos": []}, {"nome": "Molho Caesar", "alergicos": ["lactose"]}]', 
    '[{"nome": "Croutons", "alergicos": ["gluten"], "preco": 3.00}, {"nome": "Parmesão", "alergicos": ["lactose"], "preco": 2.00}]', 
    19.99, 'Salada', 1, 'salada_caesar.png'),
    
(1, 'Pizza Marguerita', 'Pizza de queijo e tomate', 
    '[{"nome": "Massa", "alergicos": ["gluten"]}, {"nome": "Queijo", "alergicos": ["lactose"]}, {"nome": "Tomate", "alergicos": []}]', 
    '[{"nome": "Azeitona", "alergicos": [], "preco": 1.50}, {"nome": "Orégano", "alergicos": [], "preco": 0.50}]', 
    29.99, 'Pizza', 1, 'pizza_marguerita.png'),
    
(1, 'Cheeseburger', 'Hambúrguer com queijo, alface e tomate', 
    '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Carne", "alergicos": []}, {"nome": "Queijo", "alergicos": ["lactose"]}, {"nome": "Alface", "alergicos": []}, {"nome": "Tomate", "alergicos": []}]', 
    '[{"nome": "Maionese", "alergicos": ["ovos"], "preco": 1.00}, {"nome": "Bacon", "alergicos": [], "preco": 2.50}]', 
    17.50, 'Lanche', 1, 'cheeseburger.png'),
    
(1, 'Salada de Frutas', 'Salada de frutas frescas da estação', 
    '[{"nome": "Maçã", "alergicos": []}, {"nome": "Banana", "alergicos": []}, {"nome": "Laranja", "alergicos": []}]', 
    '[{"nome": "Granola", "alergicos": ["gluten"], "preco": 2.00}, {"nome": "Mel", "alergicos": [], "preco": 1.50}]', 
    12.99, 'Salada', 1, 'salada_de_frutas.png'),
    
(1, 'Pizza Calabresa', 'Pizza com calabresa, queijo e cebola', 
    '[{"nome": "Massa", "alergicos": ["gluten"]}, {"nome": "Queijo", "alergicos": ["lactose"]}, {"nome": "Calabresa", "alergicos": []}, {"nome": "Cebola", "alergicos": []}]', 
    '[{"nome": "Orégano", "alergicos": [], "preco": 0.50}, {"nome": "Azeitona", "alergicos": [], "preco": 1.50}]', 
    31.99, 'Pizza', 1, 'pizza_calabresa.png'),
    
(1, 'Suco Natural', 'Suco natural de laranja', 
    '[{"nome": "Laranja", "alergicos": []}]', 
    '[{"nome": "Gelo", "alergicos": [], "preco": 0.00}, {"nome": "Açúcar", "alergicos": [], "preco": 0.00}]', 
    7.50, 'Bebida', 1, 'suco_natural.png'),
    
(1, 'Refrigerante', 'Refrigerante lata 350ml', 
    '[{"nome": "Refrigerante", "alergicos": []}]', 
    '[{"nome": "Gelo", "alergicos": [], "preco": 0.00}]', 
    5.00, 'Bebida', 1, 'refrigerante.png'),
    
(1, 'Suco Detox', 'Suco detox com couve e limão', 
    '[{"nome": "Couve", "alergicos": []}, {"nome": "Limão", "alergicos": []}]', 
    '[{"nome": "Gengibre", "alergicos": [], "preco": 0.50}, {"nome": "Mel", "alergicos": [], "preco": 1.00}]', 
    9.99, 'Bebida', 1, 'suco_detox.png');


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
(1, 'GARCOM', NOW()),
(2, 'COZINHA', NOW()),
(3, 'COZINHA', NOW());

-- Inserir dados na tabela ITENS
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(1, 1, 1, 'Sem Maiosnese', 15.99, 1),
(2, 2, 2, 'Caprichada no tempero', 19.99, 2),
(3, 3, 3, 'Borda recheada', 29.99, 3);

-- =================================
-- MAIS VALORES PARA TESTE
-- =================================

-- Inserir dados adicionais na tabela CARDAPIO

-- Categoria Lanche
INSERT INTO `PI22024`.`CARDAPIO` (`EMPRESA_ID`, `NOME`, `DESCRICAO`, `INGREDIENTES`, `ADICIONAIS`, `PRECO`, `CATEGORIA`, `STATUS`, `IMG`)
VALUES 
(1, 'Cheeseburger Duplo', 'Hambúrguer duplo com queijo', '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Carne", "alergicos": []}, {"nome": "Queijo", "alergicos": ["lactose"]}]', '[{"nome": "Bacon", "alergicos": [], "preco": 2.50}]', 20.99, 'Lanche', 1, 'cheeseburger_duplo.png'),
(1, 'X-Bacon', 'Hambúrguer com queijo e bacon', '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Carne", "alergicos": []}, {"nome": "Queijo", "alergicos": ["lactose"]}, {"nome": "Bacon", "alergicos": []}]', '[{"nome": "Maionese", "alergicos": ["ovos"], "preco": 1.00}]', 18.99, 'Lanche', 1, 'x_bacon.png'),
(1, 'Hot Dog', 'Cachorro quente com salsicha e molhos', '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Salsicha", "alergicos": []}]', '[{"nome": "Queijo ralado", "alergicos": ["lactose"], "preco": 1.50}]', 10.99, 'Lanche', 1, 'hot_dog.png'),
(1, 'Chicken Burger', 'Hambúrguer de frango com queijo', '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Frango", "alergicos": []}, {"nome": "Queijo", "alergicos": ["lactose"]}]', '[{"nome": "Alface", "alergicos": [], "preco": 1.00}]', 17.50, 'Lanche', 1, 'chicken_burger.png'),
(1, 'Veggie Burger', 'Hambúrguer vegetariano com queijo', '[{"nome": "Pão", "alergicos": ["gluten"]}, {"nome": "Hambúrguer vegetariano", "alergicos": []}, {"nome": "Queijo", "alergicos": ["lactose"]}]', '[{"nome": "Tomate", "alergicos": [], "preco": 0.50}]', 15.50, 'Lanche', 1, 'veggie_burger.png');

-- Categoria Pizza
INSERT INTO `PI22024`.`CARDAPIO` (`EMPRESA_ID`, `NOME`, `DESCRICAO`, `INGREDIENTES`, `ADICIONAIS`, `PRECO`, `CATEGORIA`, `STATUS`, `IMG`)
VALUES 
(1, 'Pizza Portuguesa', 'Pizza com presunto, ovos, e cebola', '[{"nome": "Massa", "alergicos": ["gluten"]}, {"nome": "Presunto", "alergicos": []}, {"nome": "Ovo", "alergicos": ["ovos"]}, {"nome": "Cebola", "alergicos": []}]', '[{"nome": "Orégano", "alergicos": [], "preco": 0.50}]', 34.99, 'Pizza', 1, 'pizza_portuguesa.png'),
(1, 'Pizza Quatro Queijos', 'Pizza com quatro tipos de queijo', '[{"nome": "Massa", "alergicos": ["gluten"]}, {"nome": "Queijo mussarela", "alergicos": ["lactose"]}, {"nome": "Queijo parmesão", "alergicos": ["lactose"]}, {"nome": "Queijo gorgonzola", "alergicos": ["lactose"]}]', '[{"nome": "Azeitona", "alergicos": [], "preco": 1.50}]', 36.99, 'Pizza', 1, 'pizza_quatro_queijos.png'),
(1, 'Pizza de Frango com Catupiry', 'Pizza com frango e catupiry', '[{"nome": "Massa", "alergicos": ["gluten"]}, {"nome": "Frango", "alergicos": []}, {"nome": "Catupiry", "alergicos": ["lactose"]}]', '[{"nome": "Orégano", "alergicos": [], "preco": 0.50}]', 35.99, 'Pizza', 1, 'pizza_frango_catupiry.png'),
(1, 'Pizza Pepperoni', 'Pizza com pepperoni e queijo', '[{"nome": "Massa", "alergicos": ["gluten"]}, {"nome": "Queijo", "alergicos": ["lactose"]}, {"nome": "Pepperoni", "alergicos": []}]', '[{"nome": "Azeitona", "alergicos": [], "preco": 1.50}]', 32.99, 'Pizza', 1, 'pizza_pepperoni.png');

-- Categoria Bebida
INSERT INTO `PI22024`.`CARDAPIO` (`EMPRESA_ID`, `NOME`, `DESCRICAO`, `INGREDIENTES`, `ADICIONAIS`, `PRECO`, `CATEGORIA`, `STATUS`, `IMG`)
VALUES 
(1, 'Água Mineral', 'Água mineral sem gás', '[{"nome": "Água mineral", "alergicos": []}]', '[{"nome": "Gelo", "alergicos": [], "preco": 0.00}]', 3.00, 'Bebida', 1, 'agua_mineral.png'),
(1, 'Café', 'Café expresso', '[{"nome": "Café", "alergicos": []}]', '[{"nome": "Açúcar", "alergicos": [], "preco": 0.00}]', 5.00, 'Bebida', 1, 'cafe.png'),
(1, 'Chá Gelado', 'Chá gelado de limão', '[{"nome": "Chá", "alergicos": []}, {"nome": "Limão", "alergicos": []}]', '[{"nome": "Gelo", "alergicos": [], "preco": 0.00}]', 6.50, 'Bebida', 1, 'cha_gelado.png');

-- Categoria Salada
INSERT INTO `PI22024`.`CARDAPIO` (`EMPRESA_ID`, `NOME`, `DESCRICAO`, `INGREDIENTES`, `ADICIONAIS`, `PRECO`, `CATEGORIA`, `STATUS`, `IMG`)
VALUES 
(1, 'Salada Grega', 'Salada com tomate, pepino e queijo feta', '[{"nome": "Tomate", "alergicos": []}, {"nome": "Pepino", "alergicos": []}, {"nome": "Queijo feta", "alergicos": ["lactose"]}]', '[{"nome": "Azeite", "alergicos": [], "preco": 0.50}]', 18.00, 'Salada', 1, 'salada_grega.png'),
(1, 'Salada de Atum', 'Salada com atum e alface', '[{"nome": "Atum", "alergicos": []}, {"nome": "Alface", "alergicos": []}]', '[{"nome": "Croutons", "alergicos": ["gluten"], "preco": 1.50}]', 16.50, 'Salada', 1, 'salada_de_atum.png');

-- Inserir dados adicionais na tabela MESA
INSERT INTO `PI22024`.`MESA` (`EMPRESA_ID`, `NUMERO`, `TOKEN`)
VALUES 
(1, 4, 'TOKEN_MESA_4'),
(1, 5, 'TOKEN_MESA_5'),
(1, 6, 'TOKEN_MESA_6'),
(1, 7, 'TOKEN_MESA_7');

-- Inserir dados adicionais na tabela COMANDA
INSERT INTO `PI22024`.`COMANDA` (`MESA_ID`)
VALUES 
(4),
(5),
(6),
(7);

-- Inserir dados adicionais na tabela PEDIDO
INSERT INTO `PI22024`.`PEDIDO` (`COMANDA_ID`, `STATUS`, `DATA`)
VALUES 
(4, 'COZINHA', NOW()),
(5, 'COZINHA', NOW()),
(6, 'COZINHA', NOW()),
(7, 'COZINHA', NOW());

-- Inserir dados adicionais na tabela ITENS
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(4, 4, 1, 'Extra bacon', 15.99, 1),
(5, 5, 2, 'Pouca maionese', 19.99, 2),
(6, 6, 3, 'Borda de queijo', 29.99, 3),
(7, 7, 4, 'Sem cebola', 34.99, 1);

-- Itens adicionais para o Pedido 1 (3 itens)
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(8, 1, 1, 'Extra queijo', 15.99, 3),
(9, 1, 2, 'Sem molho', 19.99, 2),
(10, 1, 3, 'Com bacon', 29.99, 1);

-- Itens adicionais para o Pedido 2 (2 itens)
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(11, 2, 2, 'Bem temperado', 19.99, 4),
(12, 2, 4, 'Extra molho', 34.99, 1);

-- Itens adicionais para o Pedido 3 (3 itens)
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(13, 3, 3, 'Borda recheada', 29.99, 3),
(15, 3, 7, 'Com açúcar', 7.50, 2),
(16, 3, 8, 'Sem gelo', 5.00, 1);

-- Itens adicionais para o Pedido 4 (3 itens)
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(17, 4, 9, 'Extra gengibre', 9.99, 2),
(20, 4, 2, 'Extra carne', 19.99, 7),
(21, 4, 3, 'Com picles', 17.50, 5);

-- Itens adicionais para o Pedido 5 (3 itens)
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(22, 5, 11, 'Sem queijo', 18.00, 5),
(23, 5, 4, 'Extra cebola', 34.99, 1),
(24, 5, 6, 'Com gelo', 12.99, 3);

-- Itens adicionais para o Pedido 6 (3 itens)
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(25, 6, 7, 'Pouco açúcar', 7.50, 1),
(27, 6, 2, 'Extra molho', 19.99, 2),
(30, 6, 1, 'Extra bacon', 15.99, 8);

-- Itens adicionais para o Pedido 7 (2 itens)
INSERT INTO `PI22024`.`ITENS` (`ID`, `PEDIDO_ID`, `CARDAPIO_ID`, `DESCRICAO`, `PRECO`, `QUANTIDADE`)
VALUES 
(31, 7, 11, 'Pouco molho', 18.00, 4),
(32, 7, 4, 'Com picles', 10.99, 5);
