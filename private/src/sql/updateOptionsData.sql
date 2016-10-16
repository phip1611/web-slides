UPDATE `%TABLE_PREFIX%%TABLE_NAME%`
SET `options`=:options,
    `command`='skip' /*damit bei "next" nicht weiter geschaltet wird nur weil man die options ändert :D*/
WHERE `id` = 1;