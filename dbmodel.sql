-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- 51 State implementation : © Pavel Kulagin (KuWizard) kuzwiz@mail.ru
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

ALTER TABLE `player` ADD `player_faction_side` TINYINT DEFAULT -1;
ALTER TABLE `player` ADD `player_fuel` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_gun` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_iron` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_brick` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_worker` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_arrow_grey` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_arrow_red` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_arrow_blue` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_arrow_uni` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_ammo` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_defence` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_devel` TINYINT DEFAULT 0;
ALTER TABLE `player` ADD `player_passed` TINYINT DEFAULT 0;

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

CREATE TABLE IF NOT EXISTS `locations`
(
    `location_id`       smallint unsigned NOT NULL AUTO_INCREMENT,
    `type`              varchar(25)       NOT NULL,
    `location_location` varchar(16)       NOT NULL,
    `location_state`    tinyint           NOT NULL,
    `activated_times`   tinyint           NOT NULL DEFAULT 0,
    `is_ruined`         tinyint           NOT NULL DEFAULT 0,
    `is_defended`       tinyint           NOT NULL DEFAULT 0,
    PRIMARY KEY (`location_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `connections`
(
    `connection_id`       smallint unsigned NOT NULL AUTO_INCREMENT,
    `type`                varchar(20)       NOT NULL,
    `connection_location` varchar(16)       NOT NULL,
    `connection_state`    tinyint           NOT NULL,
    PRIMARY KEY (`connection_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `factions`
(
    `id`            smallint unsigned NOT NULL AUTO_INCREMENT,
    `faction`       smallint          NOT NULL,
    `action_number` tinyint           NOT NULL,
    `used`          tinyint           NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `resources`
(
    `id`          smallint unsigned NOT NULL AUTO_INCREMENT,
    `location_id` tinyint           NOT NULL,
    `type`        smallint          NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;