CREATE TABLE `%TABLE_PREFIX%web-slides_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `presentation_id` int(10) UNSIGNED DEFAULT 0 NOT NULL DEFAULT '1',
  `command` char(255) NOT NULL DEFAULT 'slide:pause',
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `%TABLE_PREFIX%web-slides_sessions` ADD PRIMARY KEY (`id`);
ALTER TABLE `%TABLE_PREFIX%web-slides_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

/*Die letzte Teile ist notwendig, damit eine Zeile mit den ganzen Standardwerten eingefügt wird*/
INSERT INTO `%TABLE_PREFIX%web-slides_sessions` (`id`) VALUES ('1');