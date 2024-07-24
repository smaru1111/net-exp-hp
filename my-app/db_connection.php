<?php
function getDbConnection() {
    $db = new SQLite3('./db/timetable.db',SQLITE3_OPEN_READWRITE);
    if (!$db) {
        echo $db->lastErrorMsg();
        exit;
    }
    return $db;
}

?>
