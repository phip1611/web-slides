UPDATE `%TABLE_PREFIX%%TABLE_NAME%`
SET `command`=:command,
    `presentationIdentifier`=:presentationIdentifier,
    `options`=:options
WHERE `id` = 1;