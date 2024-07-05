-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

ALTER TABLE `player`
    ADD `player_faction` TINYINT;

CREATE TABLE IF NOT EXISTS `global_variables`
(
    `name`  varchar(255) NOT NULL,
    `value` JSON,
    PRIMARY KEY (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `user_preferences`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `player_id`  int(10)          NOT NULL,
    `pref_id`    int(10)          NOT NULL,
    `pref_value` int(10)          NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `cards`
(
    `card_id`       smallint unsigned NOT NULL AUTO_INCREMENT,
    `type`          varchar(20)       NOT NULL,
    `card_location` varchar(16)       NOT NULL,
    `card_state`    tinyint           NOT NULL,
    PRIMARY KEY (`card_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;