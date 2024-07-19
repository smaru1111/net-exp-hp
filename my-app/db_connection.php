<?php
function getDbConnection() {
    $db = new SQLite3('timetable.db');
    return $db;
}
?>
