<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include '../assets/includes/db/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success'=>false, 'message'=>'Invalid request method.']); exit;
}

if (!isset($_SESSION['userId'])) {
  echo json_encode(['success'=>false, 'message'=>'Not authenticated.']); exit;
}
$userId = $_SESSION['userId'];

if (!isset($_FILES['fileUpload']) || $_FILES['fileUpload']['error'] !== UPLOAD_ERR_OK) {
  echo json_encode(['success'=>false, 'message'=>'No file uploaded or upload error.']); exit;
}

$folderKey = isset($_POST['folder_key']) ? preg_replace('/[^a-z0-9_-]/i','',$_POST['folder_key']) : 'agriculture';
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
$uploadDir = realpath(__DIR__ . '/../uploads') . '/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$targetPath = $uploadDir . $storedName;
if (!move_uploaded_file($_FILES['fileUpload']['tmp_name'], $targetPath)) {
  echo json_encode(['success'=>false, 'message'=>'Failed to move uploaded file.']); exit;
}

$filePathForDb = '../uploads/' . $storedName;
try {
  $stmt = $conn->prepare("INSERT INTO tbl_files (filename, file_path, user_id, date_uploaded) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param('ssi', $originalName, $filePathForDb, $userId);
  $stmt->execute();
  $insertId = $stmt->insert_id;
  $stmt->close();

  $status = 'Pending';
  $remarks = null;
  $stmt2 = $conn->prepare("INSERT INTO tbl_fileaudittrails (file_id, user_id, status, remarks, time_stamp) VALUES (?, ?, ?, ?, NOW())");
  $stmt2->bind_param('iiss', $insertId, $userId, $status, $remarks);
  $stmt2->execute();
  $stmt2->close();

  echo json_encode(['success'=>true, 'message'=>'File uploaded successfully!', 'file_id'=>$insertId]);
} catch (Exception $e) {
  if (file_exists($targetPath)) unlink($targetPath);
  echo json_encode(['success'=>false, 'message'=>'Database error.']);
}
?>
