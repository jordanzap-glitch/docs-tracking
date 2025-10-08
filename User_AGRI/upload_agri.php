<?php
include '../assets/includes/session.php';
header('Content-Type: application/json; charset=utf-8');
include '../assets/includes/db/dbcon.php';

// 1. Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// 2. Validate session
if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}
$userId = intval($_SESSION['userId']);

// 3. Validate uploaded file
if (!isset($_FILES['fileUpload']) || $_FILES['fileUpload']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit;
}

// 4. Sanitize folder key and filename
$folderKey = isset($_POST['folder_key']) ? preg_replace('/[^a-z0-9_-]/i', '', $_POST['folder_key']) : 'agriculture';
$originalName = basename($_FILES['fileUpload']['name']);
$filenameSafe = preg_replace('/[^A-Za-z0-9\-\._ ]/', '_', $originalName);

$clientFilename = isset($_POST['filename']) ? trim($_POST['filename']) : '';
$ext = pathinfo($filenameSafe, PATHINFO_EXTENSION);

if ($clientFilename !== '') {
    $clientSafe = preg_replace('/[^A-Za-z0-9\-\._ ]/', '_', $clientFilename);
    $storedNameBody = $ext ? $clientSafe . '.' . $ext : $clientSafe . '_' . uniqid();
} else {
    $storedNameBody = time() . '_' . $filenameSafe;
}

$storedName = $folderKey . '_' . $storedNameBody;

// 5. Create upload directory if not exists
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
        exit;
    }
}

// 6. Move uploaded file
$targetPath = $uploadDir . $storedName;
if (!move_uploaded_file($_FILES['fileUpload']['tmp_name'], $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
    exit;
}

// 7. File path for database
$filePathForDb = '../uploads/' . $storedName; // relative path

try {
    // 8. Get user_department_id from tbl_users
    $deptQuery = $conn->prepare("SELECT department_id FROM tbl_user WHERE id = ?");
    if (!$deptQuery) throw new Exception($conn->error);
    $deptQuery->bind_param('i', $userId);
    $deptQuery->execute();
    $deptResult = $deptQuery->get_result();

    if ($deptResult->num_rows === 0) {
        throw new Exception('User not found.');
    }

    $deptRow = $deptResult->fetch_assoc();
    $departmentId = intval($deptRow['department_id']);
    $deptQuery->close();

    // 9. Insert into tbl_files
    $stmt = $conn->prepare("INSERT INTO tbl_files (filename, file_path, user_id, date_uploaded) VALUES (?, ?, ?, NOW())");
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param('ssi', $originalName, $filePathForDb, $userId);
    $stmt->execute();
    $insertId = $stmt->insert_id;
    $stmt->close();

    // 10. Insert into tbl_fileaudittrails (with user_department_id)
    $status = 'Pending';
    $remarks = null;
    $stmt2 = $conn->prepare("INSERT INTO tbl_fileaudittrails (file_id, user_id, user_department_id, status, remarks, time_stamp) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt2) throw new Exception($conn->error);
    $stmt2->bind_param('iiiss', $insertId, $userId, $departmentId, $status, $remarks);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(['success' => true, 'message' => 'File uploaded successfully!', 'file_id' => $insertId]);

} catch (Exception $e) {
    // Remove uploaded file if DB fails
    if (file_exists($targetPath)) unlink($targetPath);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
