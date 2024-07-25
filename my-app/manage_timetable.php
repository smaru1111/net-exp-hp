<?php
require 'db_connection.php';

header('Content-Type: application/json');

$db = getDbConnection();

$action = $_POST['action'];

if ($action === 'create' || $action === 'update') {
    $id = $_POST['id'];
    $student_id = $_POST['student_id'];
    $day = $_POST['day'];
    $period = $_POST['period'];
    $subject = $_POST['subject'];
}

try {
    if ($action === 'create') {
        $stmt = $db->prepare('INSERT INTO timetable (student_id, day, period, subject) VALUES (?, ?, ?, ?)');
        $stmt->bindValue(1, $student_id, SQLITE3_INTEGER);
        $stmt->bindValue(2, $day, SQLITE3_TEXT);
        $stmt->bindValue(3, $period, SQLITE3_INTEGER);
        $stmt->bindValue(4, $subject, SQLITE3_TEXT);
        if (!$stmt->execute()) {
            throw new Exception($db->lastErrorMsg());
        }
        echo json_encode(['status' => 'success']);
    } elseif ($action === 'update') {
        $stmt = $db->prepare('UPDATE timetable SET day = ?, period = ?, subject = ? WHERE id = ?');
        $stmt->bindValue(1, $day, SQLITE3_TEXT);
        $stmt->bindValue(2, $period, SQLITE3_INTEGER);
        $stmt->bindValue(3, $subject, SQLITE3_TEXT);
        $stmt->bindValue(4, $id, SQLITE3_INTEGER);
        if (!$stmt->execute()) {
            throw new Exception($db->lastErrorMsg());
        }
        echo json_encode(['status' => 'success']);
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare('DELETE FROM timetable WHERE id = ?');
        $stmt->bindValue(1, $id, SQLITE3_INTEGER);
        if (!$stmt->execute()) {
            throw new Exception($db->lastErrorMsg());
        }
        echo json_encode(['status' => 'success']);
    } elseif ($action === 'fetch') {
        $student_id = $_POST['student_id'];
        $stmt = $db->prepare('SELECT * FROM timetable WHERE student_id = ? ORDER BY day, period');
        $stmt->bindValue(1, $student_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $timetable = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $timetable[] = $row;
        }
        echo json_encode($timetable);
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
