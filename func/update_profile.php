<?php
session_start();
require_once '../connection.php';

class User {
    protected $id;
    protected $role;
    protected $db;
    
    public function __construct($userId, $role, $db) {
        $this->id = $userId;
        $this->role = $role;
        $this->db = $db;
    }
    
    protected function getCommonFields($postData) {
        return [
            'nama_lengkap' => $postData['nama_lengkap'],
            'jenis_kelamin' => $postData['jenis_kelamin'],
            'no_hp' => $postData['no_hp'],
            'email' => $postData['email']
        ];
    }
}

class Mahasiswa extends User {
    public function updateProfile($postData) {
        $query = "UPDATE mahasiswa SET 
                    nama_lengkap = :nama_lengkap,
                    jenis_kelamin = :jenis_kelamin,
                    no_hp = :no_hp,
                    no_hp_ortu = :no_hp_ortu,
                    email = :email
                 WHERE id = :user_id";
                 
        $params = array_merge($this->getCommonFields($postData), [
            'no_hp_ortu' => $postData['no_hp_ortu'],
            'user_id' => $this->id
        ]);
        
        return ['query' => $query, 'params' => $params];
    }
}

class Staff extends User {
    public function updateProfile($postData) {
        $query = "UPDATE {$this->role} SET 
                    nama_lengkap = :nama_lengkap,
                    jenis_kelamin = :jenis_kelamin,
                    no_hp = :no_hp,
                    jurusan = :jurusan,
                    prodi = :prodi,
                    profesi = :profesi,
                    email = :email
                 WHERE id = :user_id";
                 
        $params = array_merge($this->getCommonFields($postData), [
            'jurusan' => $postData['jurusan'],
            'prodi' => $postData['prodi'],
            'profesi' => $postData['profesi'],
            'user_id' => $this->id
        ]);
        
        return ['query' => $query, 'params' => $params];
    }
}

class ProfileUpdater {
    private $db;
    private $user;
    
    public function __construct($db, $userId, $role) {
        $this->db = $db;
        $this->user = $this->createUserInstance($userId, $role, $db);
    }
    
    private function createUserInstance($userId, $role, $db) {
        switch($role) {
            case 'mahasiswa':
                return new Mahasiswa($userId, $role, $db);
            case 'dosen':
            case 'admin':
                return new Staff($userId, $role, $db);
            default:
                throw new Exception("Invalid user role");
        }
    }
    
    public function update($postData) {
        try {
            $updateData = $this->user->updateProfile($postData);
            $stmt = $this->db->prepare($updateData['query']);
            
            if ($stmt->execute($updateData['params'])) {
                $_SESSION['success_message'] = "Profile updated successfully";
            } else {
                $_SESSION['error_message'] = "Failed to update profile";
            }
            
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
    }
}

// Main execution
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profileUpdater = new ProfileUpdater($koneksi, $_SESSION['user_id'], $_SESSION['role']);
    $profileUpdater->update($_POST);
    
    header("Location: ../{$_SESSION['role']}/dashboard.php");
    exit();
}
