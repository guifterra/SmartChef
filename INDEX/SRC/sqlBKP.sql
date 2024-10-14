-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;

SET
    @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS,
    FOREIGN_KEY_CHECKS = 0;

SET
    @OLD_SQL_MODE = @@SQL_MODE,
    SQL_MODE = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema PI22024
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema PI22024
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `PI22024`;

CREATE SCHEMA IF NOT EXISTS `PI22024` DEFAULT CHARACTER SET utf8;

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
    `DESCRICAO` TEXT NULL,
    `INGREDIENTES` TEXT NOT NULL,
    `PRECO` DOUBLE NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_CARDAPIO_EMPRESA1_idx` (`EMPRESA_ID` ASC),
    CONSTRAINT `fk_CARDAPIO_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
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
    `VALIDO` TINYINT NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID`),
    UNIQUE INDEX `GAR_NOME_UNIQUE` (`USERNAME` ASC),
    INDEX `fk_USER_EMPRESA1_idx` (`EMPRESA_ID` ASC),
    CONSTRAINT `fk_USER_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`MESA`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`MESA` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `EMPRESA_ID` INT NOT NULL,
    `NUMERO` INT NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_MESA_EMPRESA1_idx` (`EMPRESA_ID` ASC),
    CONSTRAINT `fk_MESA_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`COMANDA`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`COMANDA` (
    `ID` INT NOT NULL AUTO_INCREMENT,
    `MESA_ID` INT NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_COMANDA_MESA1_idx` (`MESA_ID` ASC),
    CONSTRAINT `fk_COMANDA_MESA1` FOREIGN KEY (`MESA_ID`) REFERENCES `PI22024`.`MESA` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
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
    PRIMARY KEY (`ID`),
    INDEX `fk_PEDIDO_COMANDA1_idx` (`COMANDA_ID` ASC),
    CONSTRAINT `fk_PEDIDO_COMANDA1` FOREIGN KEY (`COMANDA_ID`) REFERENCES `PI22024`.`COMANDA` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
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
    CONSTRAINT `fk_HISTORICO_EMPRESA1` FOREIGN KEY (`EMPRESA_ID`) REFERENCES `PI22024`.`EMPRESA` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `PI22024`.`ITENS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `PI22024`.`ITENS` (
    `ID` INT NOT NULL,
    `PEDIDO_ID` INT NOT NULL,
    `CARDAPIO_ID` INT NOT NULL,
    `DESCRICAO` TEXT NOT NULL,
    PRIMARY KEY (`ID`),
    INDEX `fk_ITENS_PEDIDO1_idx` (`PEDIDO_ID` ASC),
    INDEX `fk_ITENS_CARDAPIO1_idx` (`CARDAPIO_ID` ASC),
    CONSTRAINT `fk_ITENS_PEDIDO1` FOREIGN KEY (`PEDIDO_ID`) REFERENCES `PI22024`.`PEDIDO` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_ITENS_CARDAPIO1` FOREIGN KEY (`CARDAPIO_ID`) REFERENCES `PI22024`.`CARDAPIO` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;

SET SQL_MODE = @OLD_SQL_MODE;

SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;

SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;