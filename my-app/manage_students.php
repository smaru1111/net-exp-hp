<?php
require 'db_connection.php';

header('Content-Type: application/json');

$db = getDbConnection();

$action = $_POST['action'];

try {
    if ($action === 'add') {
        $student_number = $_POST['student_number'];
        $name = $_POST['name'];
        $stmt = $db->prepare('INSERT INTO students (name, student_number) VALUES (?, ?)');
        $stmt->bindValue(1, $name, SQLITE3_TEXT);
        $stmt->bindValue(2, $student_number, SQLITE3_TEXT);
        if (!$stmt->execute()) {
            throw new Exception($db->lastErrorMsg());
        }
        echo json_encode(['success' => true]);
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $student_number = $_POST['student_number'];
        $name = $_POST['name'];
        $stmt = $db->prepare('UPDATE students SET name = ?, student_number = ? WHERE id = ?');
        $stmt->bindValue(1, $name, SQLITE3_TEXT);
        $stmt->bindValue(2, $student_number, SQLITE3_TEXT);
        $stmt->bindValue(3, $id, SQLITE3_INTEGER);
        if (!$stmt->execute()) {
            throw new Exception($db->lastErrorMsg());
        }
        echo json_encode(['success' => true]);
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare('DELETE FROM students WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        if (!$stmt->execute()) {
            throw new Exception($db->lastErrorMsg());
        }
        echo json_encode(['success' => true]);
    } elseif ($action === 'fetch') {
        $result = $db->query('SELECT * FROM students');
        $students = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $students[] = $row;
        }
        echo json_encode($students);
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
