UPDATE `%TABLE_PREFIX%%TABLE_NAME%`
SET `command`=:command,
    `presentationIdentifier`=:presentationIdentifier,
    `options`=:options
WHERE `id` = 1;
/*
überall x als Präfix, weil "options" ein reserviertes
Keywort ist und dann null in die DB geschrieben wird :D
*/