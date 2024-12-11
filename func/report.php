<?php
session_start();
require_once '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        
        // Get form data
        $name = $_POST['name'];
        $nama_pelanggaran = $_POST['nama_pelanggaran'];
        $waktu = $_POST['waktu'];
        $lokasi = $_POST['lokasi'];
        

        // Handle file upload
        $bukti = '';
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/evidence/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $targetPath)) {
                $bukti = $fileName;
            } else {
                throw new Exception('Failed to upload file');
            }
        }

// Get form data
$waktu = date('Y-m-d H:i:s', strtotime($_POST['waktu']));

// Use parameterized query
$query = "INSERT INTO report (name, bukti, nama_pelanggaran, waktu, lokasi) 
         VALUES (:name, :bukti, :nama_pelanggaran, :waktu, :lokasi)";

        $stmt = $koneksi->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':bukti', $bukti);
        $stmt->bindParam(':nama_pelanggaran', $nama_pelanggaran);
        $stmt->bindParam(':waktu', $waktu);
        $stmt->bindParam(':lokasi', $lokasi);
        
        $stmt->execute();
        
        // Get the last inserted ID
        $reportId = $koneksi->lastInsertId();

        // Insert into history table with default status
        $status = 'Pending';
        $bobot = 'TBD';
        $hukuman = 'TBD';
        
        $historyQuery = "INSERT INTO history (fk_report, status, bobot, hukuman) 
                        VALUES (:fk_report, :status, :bobot, :hukuman)";
        
        $historyStmt = $koneksi->prepare($historyQuery);
        $historyStmt->bindParam(':fk_report', $reportId);
        $historyStmt->bindParam(':status', $status);
        $historyStmt->bindParam(':bobot', $bobot);
        $historyStmt->bindParam(':hukuman', $hukuman);
        
        $historyStmt->execute();

        echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
