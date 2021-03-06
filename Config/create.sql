
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- canada_post_order
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canada_post_order`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `address_id` INTEGER,
    `order_address_id` INTEGER,
    `service_id` INTEGER,
    `options` VARCHAR(255),
    PRIMARY KEY (`id`),
    INDEX `FI_canada_post_service_id` (`service_id`),
    INDEX `FI_canada_post_address_id` (`address_id`),
    INDEX `FI_canada_post_order_address_id` (`order_address_id`),
    CONSTRAINT `fk_canada_post_service_id`
        FOREIGN KEY (`service_id`)
        REFERENCES `canada_post_service` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `fk_canada_post_address_id`
        FOREIGN KEY (`address_id`)
        REFERENCES `address` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `fk_canada_post_order_address_id`
        FOREIGN KEY (`order_address_id`)
        REFERENCES `order_address` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- canada_post_service
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canada_post_service`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `visible` TINYINT DEFAULT 0 NOT NULL,
    `code` VARCHAR(255),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- canada_post_service_i18n
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canada_post_service_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `chapo` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `canada_post_service_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `canada_post_service` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
