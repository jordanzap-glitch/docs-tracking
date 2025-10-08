<?php
include '../assets/includes/db/dbcon.php';

$folder_key = isset($_GET['folder_key']) ? $_GET['folder_key'] : '';

// Fetch file info, uploader, department, and status
$query = $conn->prepare("
    SELECT 
        f.id,
        f.filename,
        f.file_path,
        f.date_uploaded,
        u.firstname,
        u.lastname,
        a.status,
        d.department_name
    FROM tbl_files f
    LEFT JOIN tbl_user u ON f.user_id = u.id
    LEFT JOIN tbl_fileaudittrails a ON f.id = a.file_id
    LEFT JOIN tbl_departments d ON a.user_department_id = d.id
    WHERE f.file_path LIKE CONCAT('%', ?, '%')
    ORDER BY f.date_uploaded DESC
");

$query->bind_param("s", $folder_key);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $uploadedBy = trim($row['firstname'] . ' ' . $row['lastname']);
        if ($uploadedBy === '') $uploadedBy = 'Unknown';

        $department = !empty($row['department_name']) ? $row['department_name'] : 'N/A';
        $status = !empty($row['status']) ? $row['status'] : 'Pending';

        echo '<tr>
                <td>' . htmlspecialchars($row['filename']) . '</td>
                <td>' . htmlspecialchars($uploadedBy) . '</td>
                <td>' . htmlspecialchars($department) . '</td>
                <td>' . htmlspecialchars($row['date_uploaded']) . '</td>
                <td>' . htmlspecialchars($status) . '</td>
                <td class="text-center">
                    <a href="' . htmlspecialchars($row['file_path']) . '" class="btn btn-outline-success btn-sm download-icon" download title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                </td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="6" class="text-center text-muted">No files uploaded yet.</td></tr>';
}
?>
