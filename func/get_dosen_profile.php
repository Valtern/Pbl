<?php
session_start();
require_once '../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT nama_lengkap, jenis_kelamin, no_hp, jurusan, prodi, profesi, email 
              FROM dosen 
              WHERE id = :user_id";
              
    $stmt = $koneksi->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
