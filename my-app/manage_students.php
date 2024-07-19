<?php
require 'db_connection.php';

$db = getDbConnection();

$action = $_POST['action'];

if ($action === 'add' || $action === 'update') {
    $student_number = $_POST['student_number'];
    $name = $_POST['name'];
}

if ($action === 'add') {
    $stmt = $db->prepare('INSERT INTO students (name, student_number) VALUES (?, ?)');
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $student_number, SQLITE3_TEXT);
    $stmt->execute();
} elseif ($action === 'update') {
    $id = $_POST['id'];
    $stmt = $db->prepare('UPDATE students SET name = ?, student_number = ? WHERE id = ?');
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $student_number, SQLITE3_TEXT);
    $stmt->bindValue(3, $id, SQLITE3_INTEGER);
    $stmt->execute();
} elseif ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $db->prepare('DELETE FROM students WHERE id = ?');
    $stmt->bindValue(1, $id, SQLITE3_INTEGER);
    $stmt->execute();
} elseif ($action === 'fetch') {
    $result = $db->query('SELECT * FROM students');
    $students = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $students[] = $row;
    }
    echo json_encode($students);
    exit;
}

header('Location: index.html');
?>
