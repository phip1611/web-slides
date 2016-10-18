UPDATE `%TABLE_PREFIX%%TABLE_NAME%`
SET `command`=:command,
    `presentation_identifier`=:presentationIdentifier,
    `options`=:options
WHERE `id` = 1;