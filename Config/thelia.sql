
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- canada_post_order
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `canada_post_order`;

CREATE TABLE `canada_post_order`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `address_id` INTEGER,
    `order_address_id` INTEGER,
    `service` VARCHAR(255) NOT NULL,
    `options` VARCHAR(255),
    PRIMARY KEY (`id`),
    INDEX `FI_canada_post_address_id` (`address_id`),
    INDEX `FI_canada_post_order_address_id` (`order_address_id`),
    CONSTRAINT `fk_canada_post_address_id`
        FOREIGN KEY (`address_id`)
        REFERENCES `address` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `fk_canada_post_order_address_id`
        FOREIGN KEY (`order_address_id`)
        REFERENCES `order_address` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
