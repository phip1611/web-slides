CREATE TABLE `%TABLE_PREFIX%%TABLE_NAME%`
( `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT , `referenceDatetime` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Datetime/Timestamp anhand dessen über Rest festgestellt werden kann, ob es eine Änderung am Server gab.' ,
  `command` CHAR(255) NOT NULL DEFAULT '' ,
  `presentationIdentifier` CHAR(255) NOT NULL DEFAULT '' COMMENT 'Wenn es später im HTML-Dokument mal mehrere Präsentationen geben sollte, dann wird hierrüber die entsprechende Präsentation angegeben.',
  `options` TEXT NOT NULL DEFAULT '' COMMENT 'Wird ein JSON beinhalten.',
   PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;