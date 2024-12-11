<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $query = "SELECT r.* 
              FROM report r 
              WHERE r.id = :report_id 
              AND r.name = (SELECT nama_lengkap FROM mahasiswa WHERE id = :user_id)";
    
    $stmt = $koneksi->prepare($query);
    $stmt->bindParam(':report_id', $_GET['id']);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode([
            'success' => true,
            'data' => $row
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Report not found'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
