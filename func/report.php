<?php
session_start();
require_once '../connection.php';

class FileUploader {
    private $uploadDir;
    
    public function __construct($uploadDir = '../uploads/evidence/') {
        $this->uploadDir = $uploadDir;
    }
    
    public function upload($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $targetPath = $this->uploadDir . $fileName;
        
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload file');
        }
        
        return $fileName;
    }
}

class Report {
    private $db;
    private $data;
    
    public function __construct($db, $formData, $bukti) {
        $this->db = $db;
        $this->data = [
            'name' => $formData['name'],
            'bukti' => $bukti,
            'nama_pelanggaran' => $formData['nama_pelanggaran'],
            'waktu' => date('Y-m-d H:i:s', strtotime($formData['waktu'])),
            'lokasi' => $formData['lokasi']
        ];
    }
    
    public function save() {
        $query = "INSERT INTO report (name, bukti, nama_pelanggaran, waktu, lokasi) 
                 VALUES (:name, :bukti, :nama_pelanggaran, :waktu, :lokasi)";
                 
        $stmt = $this->db->prepare($query);
        $stmt->execute($this->data);
        
        return $this->db->lastInsertId();
    }
}

class History {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createInitialRecord($reportId) {
        $historyData = [
            'fk_report' => $reportId,
            'status' => 'Pending',
            'bobot' => 'TBD',
            'hukuman' => 'TBD'
        ];
        
        $query = "INSERT INTO history (fk_report, status, bobot, hukuman) 
                 VALUES (:fk_report, :status, :bobot, :hukuman)";
                 
        $stmt = $this->db->prepare($query);
        $stmt->execute($historyData);
    }
}

class ReportSubmissionHandler {
    private $db;
    private $fileUploader;
    
    public function __construct($db) {
        $this->db = $db;
        $this->fileUploader = new FileUploader();
    }
    
    public function handle($postData, $files) {
        try {
            $this->db->beginTransaction();
            
            // Handle file upload
            $bukti = $this->fileUploader->upload($files['bukti']);
            
            // Create and save report
            $report = new Report($this->db, $postData, $bukti);
            $reportId = $report->save();
            
            // Create history record
            $history = new History($this->db);
            $history->createInitialRecord($reportId);
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Report submitted successfully'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

// Main execution
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new ReportSubmissionHandler($koneksi);
    $result = $handler->handle($_POST, $_FILES);
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
