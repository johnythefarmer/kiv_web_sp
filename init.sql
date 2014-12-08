SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema trophy
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `trophy` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `trophy` ;

-- -----------------------------------------------------
-- Table `trophy`.`mistni_organizace`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trophy`.`mistni_organizace` ;

CREATE TABLE IF NOT EXISTS `trophy`.`mistni_organizace` (
  `mo_id` BIGINT NOT NULL AUTO_INCREMENT,
  `nazev` VARCHAR(45) NULL,
  `website` VARCHAR(45) NULL,
  `logo_url` VARCHAR(45) NULL,
  PRIMARY KEY (`mo_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `trophy`.`uzivatel`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trophy`.`uzivatel` ;

CREATE TABLE IF NOT EXISTS `trophy`.`uzivatel` (
  `user_id` BIGINT NOT NULL AUTO_INCREMENT,
  `nickname` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `jmeno` VARCHAR(45) NULL,
  `prijmeni` VARCHAR(45) NULL,
  `photo_url` VARCHAR(45) NULL,
  `web` VARCHAR(45) NULL,
  `is_admin` TINYINT(1) NULL,
  `d_registrace` DATE NULL,
  `mo_id` BIGINT NOT NULL,
  PRIMARY KEY (`user_id`),
  INDEX `fk_uzivatel_mistni_organizace1_idx` (`mo_id` ASC),
  UNIQUE INDEX `nickname_UNIQUE` (`nickname` ASC),
  CONSTRAINT `fk_uzivatel_mistni_organizace1`
    FOREIGN KEY (`mo_id`)
    REFERENCES `trophy`.`mistni_organizace` (`mo_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `trophy`.`druh`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trophy`.`druh` ;

CREATE TABLE IF NOT EXISTS `trophy`.`druh` (
  `druh_id` BIGINT NOT NULL AUTO_INCREMENT,
  `nazev_rod` VARCHAR(45) NULL,
  `nazev_druh` VARCHAR(45) NULL,
  `vaha_trofej` VARCHAR(45) NULL,
  `velikost_trofej` VARCHAR(45) NULL,
  PRIMARY KEY (`druh_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `trophy`.`reka`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trophy`.`reka` ;

CREATE TABLE IF NOT EXISTS `trophy`.`reka` (
  `reka_id` BIGINT NOT NULL AUTO_INCREMENT,
  `nazev` VARCHAR(45) NULL,
  PRIMARY KEY (`reka_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `trophy`.`revir`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trophy`.`revir` ;

CREATE TABLE IF NOT EXISTS `trophy`.`revir` (
  `revir_id` BIGINT NOT NULL,
  `mo_id` BIGINT NOT NULL,
  `cislo` VARCHAR(5) NOT NULL,
  `podrevir` INT NOT NULL,
  `reka_id` BIGINT NOT NULL,
  PRIMARY KEY (`revir_id`, `podrevir`),
  INDEX `fk_revir_reka1_idx` (`reka_id` ASC),
  INDEX `fk_revir_mistni_organizace1_idx` (`mo_id` ASC),
  CONSTRAINT `fk_revir_reka1`
    FOREIGN KEY (`reka_id`)
    REFERENCES `trophy`.`reka` (`reka_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_revir_mistni_organizace1`
    FOREIGN KEY (`mo_id`)
    REFERENCES `trophy`.`mistni_organizace` (`mo_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `trophy`.`ulovek`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trophy`.`ulovek` ;

CREATE TABLE IF NOT EXISTS `trophy`.`ulovek` (
  `ulovek_id` BIGINT NOT NULL AUTO_INCREMENT,
  `d_chyceni` DATE NULL,
  `velikost` INT NULL,
  `vaha` FLOAT NULL,
  `photo_url` VARCHAR(45) NULL,
  `druh_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `d_post` DATETIME NULL,
  `revir_id` BIGINT NOT NULL,
  `podrevir` INT NOT NULL,
  PRIMARY KEY (`ulovek_id`),
  INDEX `fk_ulovek_druh1_idx` (`druh_id` ASC),
  INDEX `fk_ulovek_uzivatel1_idx` (`user_id` ASC),
  INDEX `fk_ulovek_revir1_idx` (`revir_id` ASC, `podrevir` ASC),
  CONSTRAINT `fk_ulovek_druh1`
    FOREIGN KEY (`druh_id`)
    REFERENCES `trophy`.`druh` (`druh_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ulovek_uzivatel1`
    FOREIGN KEY (`user_id`)
    REFERENCES `trophy`.`uzivatel` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ulovek_revir1`
    FOREIGN KEY (`revir_id` , `podrevir`)
    REFERENCES `trophy`.`revir` (`revir_id` , `podrevir`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `trophy`.`uzivatel_likes_ulovek`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `trophy`.`uzivatel_likes_ulovek` ;

CREATE TABLE IF NOT EXISTS `trophy`.`uzivatel_likes_ulovek` (
  `user_id` BIGINT NOT NULL,
  `ulovek_id` BIGINT NOT NULL,
  `d_like` DATETIME NULL,
  PRIMARY KEY (`user_id`, `ulovek_id`),
  INDEX `fk_uzivatel_has_ulovek_ulovek1_idx` (`ulovek_id` ASC),
  INDEX `fk_uzivatel_has_ulovek_uzivatel_idx` (`user_id` ASC),
  CONSTRAINT `fk_uzivatel_has_ulovek_uzivatel`
    FOREIGN KEY (`user_id`)
    REFERENCES `trophy`.`uzivatel` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_uzivatel_has_ulovek_ulovek1`
    FOREIGN KEY (`ulovek_id`)
    REFERENCES `trophy`.`ulovek` (`ulovek_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

USE `trophy` ;

-- -----------------------------------------------------
-- Placeholder table for view `trophy`.`topYear`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trophy`.`topYear` (`ulovek_id` INT, `velikost` INT, `vaha` INT, `photo_url` INT, `datum` INT, `koef` INT, `nazev_rod` INT, `nazev_druh` INT, `nazev_revir` INT, `nickname` INT, `user_id` INT, `revir_id` INT, `podrevir` INT);

-- -----------------------------------------------------
-- Placeholder table for view `trophy`.`topMonth`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trophy`.`topMonth` (`ulovek_id` INT, `velikost` INT, `vaha` INT, `photo_url` INT, `datum` INT, `koef` INT, `nazev_rod` INT, `nazev_druh` INT, `nazev_revir` INT, `nickname` INT, `user_id` INT, `revir_id` INT, `podrevir` INT);

-- -----------------------------------------------------
-- Placeholder table for view `trophy`.`topWeek`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trophy`.`topWeek` (`ulovek_id` INT, `velikost` INT, `vaha` INT, `photo_url` INT, `datum` INT, `koef` INT, `nazev_rod` INT, `nazev_druh` INT, `nazev_revir` INT, `nickname` INT, `user_id` INT, `revir_id` INT, `podrevir` INT);

-- -----------------------------------------------------
-- Placeholder table for view `trophy`.`topDay`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trophy`.`topDay` (`ulovek_id` INT, `velikost` INT, `vaha` INT, `photo_url` INT, `datum` INT, `koef` INT, `nazev_rod` INT, `nazev_druh` INT, `nazev_revir` INT, `nickname` INT, `user_id` INT, `revir_id` INT, `podrevir` INT);

-- -----------------------------------------------------
-- Placeholder table for view `trophy`.`poctyMO`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `trophy`.`poctyMO` (`mo_id` INT, `nazev` INT, `pocet_uzivatelu` INT, `pocet_ulovku` INT);

-- -----------------------------------------------------
-- View `trophy`.`topYear`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `trophy`.`topYear` ;
DROP TABLE IF EXISTS `trophy`.`topYear`;
USE `trophy`;
CREATE  OR REPLACE VIEW `topYear` AS
SELECT ul.ulovek_id, ul.velikost, ul.vaha, ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
            ((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef, d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
            uz.nickname, uz.user_id, ul.revir_id, ul.podrevir
            FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
            inner join uzivatel uz on ul.user_id = uz.user_id
            inner join druh d on ul.druh_id = d.druh_id
            inner join reka on r.reka_id = reka.reka_id
			where timestampdiff( day ,d_post, now()) <=365
            order by koef desc
			limit 10;

-- -----------------------------------------------------
-- View `trophy`.`topMonth`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `trophy`.`topMonth` ;
DROP TABLE IF EXISTS `trophy`.`topMonth`;
USE `trophy`;
CREATE  OR REPLACE VIEW `topMonth` AS
SELECT ul.ulovek_id, ul.velikost, ul.vaha, ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
            ((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef, d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
            uz.nickname, uz.user_id, ul.revir_id, ul.podrevir
            FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
            inner join uzivatel uz on ul.user_id = uz.user_id
            inner join druh d on ul.druh_id = d.druh_id
            inner join reka on r.reka_id = reka.reka_id
			where timestampdiff( day ,d_post, now()) <=30
            order by koef desc
			limit 10;

-- -----------------------------------------------------
-- View `trophy`.`topWeek`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `trophy`.`topWeek` ;
DROP TABLE IF EXISTS `trophy`.`topWeek`;
USE `trophy`;
CREATE  OR REPLACE VIEW `topWeek` AS
SELECT ul.ulovek_id, ul.velikost, ul.vaha, ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
            ((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef, d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
            uz.nickname, uz.user_id, ul.revir_id, ul.podrevir
            FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
            inner join uzivatel uz on ul.user_id = uz.user_id
            inner join druh d on ul.druh_id = d.druh_id
            inner join reka on r.reka_id = reka.reka_id
			where timestampdiff( day ,d_post, now()) <=7
            order by koef desc
			limit 10;

-- -----------------------------------------------------
-- View `trophy`.`topDay`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `trophy`.`topDay` ;
DROP TABLE IF EXISTS `trophy`.`topDay`;
USE `trophy`;
CREATE  OR REPLACE VIEW `topDay` AS
SELECT ul.ulovek_id, ul.velikost, ul.vaha, ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
            ((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef, d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
            uz.nickname, uz.user_id, ul.revir_id, ul.podrevir
            FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
            inner join uzivatel uz on ul.user_id = uz.user_id
            inner join druh d on ul.druh_id = d.druh_id
            inner join reka on r.reka_id = reka.reka_id
			where timestampdiff( hour ,d_post, now()) <=24
            order by koef desc
			limit 10;

-- -----------------------------------------------------
-- View `trophy`.`poctyMO`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `trophy`.`poctyMO` ;
DROP TABLE IF EXISTS `trophy`.`poctyMO`;
USE `trophy`;
CREATE  OR REPLACE VIEW `poctyMO` AS
SELECT mo.mo_id,mo.nazev, count(uz.user_id) as pocet_uzivatelu,  count(ul.ulovek_id) as pocet_ulovku FROM trophy.mistni_organizace mo inner join trophy.revir r on mo.mo_id = r.mo_id left join ulovek ul on ul.revir_id = r.revir_id left join trophy.uzivatel uz on uz.mo_id = mo.mo_id group by mo.mo_id order by pocet_ulovku desc, pocet_uzivatelu desc;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;