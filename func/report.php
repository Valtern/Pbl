<?php
session_start();
require_once '../connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../mahasiswa/dashboard.php');
    exit();
}

try {
    $koneksi->beginTransaction();

    // Generate unique violation number
    $violation_number = 'VIO' . date('YmdHis') . rand(100, 999);
    
    // Insert into report_hist
    $query = "INSERT INTO report_hist (no_pelanggar, nama_pelanggaran, status, id_pelanggar) 
              SELECT :violation_number, violation_description, 'Pending', :reported_student 
              FROM violation WHERE id = :violation_id";
    
    $stmt = $koneksi->prepare($query);
    $stmt->bindParam(':violation_number', $violation_number);
    $stmt->bindParam(':reported_student', $_POST['reported_student']);
    $stmt->bindParam(':violation_id', $_POST['violation_id']);
    $stmt->execute();

    // Insert into report_hist_detail
    $query = "INSERT INTO report_hist_detail (mhsw_id, waktu, lokasi, report_hist) 
              VALUES (:reporter_id, :incident_time, :location, :violation_number)";
    
    $stmt = $koneksi->prepare($query);
    $stmt->bindParam(':reporter_id', $_SESSION['user_id']);
    $stmt->bindParam(':incident_time', $_POST['incident_time']);
    $stmt->bindParam(':location', $_POST['location']);
    $stmt->bindParam(':violation_number', $violation_number);
    $stmt->execute();

    // Handle file upload
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/evidence/';
        $file_extension = pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION);
        $new_filename = $violation_number . '.' . $file_extension;
        move_uploaded_file($_FILES['evidence']['tmp_name'], $upload_dir . $new_filename);
    }

    $koneksi->commit();
    header('Location: ../mahasiswa/dashboard.php?report_status=success');

} catch (PDOException $e) {
    $koneksi->rollBack();
    header('Location: ../mahasiswa/dashboard.php?report_status=error&message=' . urlencode($e->getMessage()));
}
?>
