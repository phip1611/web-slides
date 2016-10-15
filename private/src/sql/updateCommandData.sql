UPDATE `%TABLE_PREFIX%%TABLE_NAME%`
SET `command`=:command,
    `referenceDatetime`=NOW()/*Trick um bei gleich bleibendem Kommando die referenceDatetime (ON UPDATE CURRENT TIMESTAMP) ändern! */
    /* Notwendig damit man mehrere "next" hintereinander ausführen kann */
WHERE `id` = 1;
