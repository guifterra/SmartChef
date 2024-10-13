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
        'GARCON',
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