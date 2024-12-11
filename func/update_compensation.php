<?php
session_start();
require_once '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $history_id = $_POST['history_id'];
        $bobot = $_POST['bobot'];
        $hukuman = $_POST['hukuman'];
        $status = $_POST['status'];

        $query = "UPDATE history 
                 SET bobot = :bobot, 
                     hukuman = :hukuman, 
                     status = :status 
                 WHERE id = :history_id";
                 
        $stmt = $koneksi->prepare($query);
        $stmt->execute([
            ':bobot' => $bobot,
            ':hukuman' => $hukuman,
            ':status' => $status,
            ':history_id' => $history_id
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
