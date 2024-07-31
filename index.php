<?php
// index.php
header('Content-Type: application/json');
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$resource = array_shift($request);
$id = array_shift($request);

switch ($resource) {
    case 'students':
        handleStudents($method, $id);
        break;
    case 'teachers':
        handleTeachers($method, $id);
        break;
    case 'courses':
        handleCourses($method, $id);
        break;
    case 'news':
        handleNews($method, $id);
        break;
    case 'feedbacks':
        handleFeedbacks($method, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
}

function handleStudents($method, $id) {
    global $pdo;
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
                $stmt->execute([$id]);
                $student = $stmt->fetch();
                echo json_encode($student);
            } else {
                $stmt = $pdo->query("SELECT * FROM students");
                $students = $stmt->fetchAll();
                echo json_encode($students);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['email']]);
            echo json_encode(['id' => $pdo->lastInsertId()]);
            break;
        case 'PUT':
            if ($id) {
                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$data['name'], $data['email'], $id]);
                echo json_encode(['status' => 'success']);
            }
            break;
        case 'DELETE':
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['status' => 'success']);
            }
            break;
    }
}

// Similar functions for teachers, courses, news, and feedbacks

function handleTeachers($method, $id) {
    // Implement similar to handleStudents
}

function handleCourses($method, $id) {
    // Implement similar to handleStudents
}

function handleNews($method, $id) {
    // Implement similar to handleStudents
}

function handleFeedbacks($method, $id) {
    global $pdo;
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM feedbacks WHERE id = ?");
                $stmt->execute([$id]);
                $feedback = $stmt->fetch();
                echo json_encode($feedback);
            } else {
                if (isset($_GET['student_id'])) {
                    $stmt = $pdo->prepare("SELECT * FROM feedbacks WHERE student_id = ?");
                    $stmt->execute([$_GET['student_id']]);
                    $feedbacks = $stmt->fetchAll();
                    echo json_encode($feedbacks);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'student_id required']);
                }
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO feedbacks (student_id, course_id, feedback) VALUES (?, ?, ?)");
            $stmt->execute([$data['student_id'], $data['course_id'], $data['feedback']]);
            echo json_encode(['id' => $pdo->lastInsertId()]);
            break;
        case 'PUT':
            if ($id) {
                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("UPDATE feedbacks SET feedback = ? WHERE id = ?");
                $stmt->execute([$data['feedback'], $id]);
                echo json_encode(['status' => 'success']);
            }
            break;
        case 'DELETE':
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM feedbacks WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['status' => 'success']);
            }
            break;
    }
}
?>
