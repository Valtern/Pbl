<?php
session_start();
require_once '../connection.php';

class User {
    protected $id;
    protected $nama_lengkap;
    protected $role;
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function authenticate($email, $password) {
        $roles = ['mahasiswa', 'admin', 'dosen'];
        
        foreach ($roles as $role) {
            $query = "SELECT id, nama_lengkap, '$role' as role 
                     FROM $role 
                     WHERE email = ? AND password = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$email, $password]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $this->id = $user['id'];
                $this->nama_lengkap = $user['nama_lengkap'];
                $this->role = $user['role'];
                return true;
            }
        }
        return false;
    }
    
    public function createSession() {
        $_SESSION['user_id'] = $this->id;
        $_SESSION['nama_lengkap'] = $this->nama_lengkap;
        $_SESSION['role'] = $this->role;
    }
    
    public function getRedirectPath() {
        return "../{$this->role}/dashboard.php";
    }
}

class ProfileFetcher {
    private $db;
    private $userId;
    private $role;
    
    public function __construct($db, $userId, $role) {
        $this->db = $db;
        $this->userId = $userId;
        $this->role = $role;
    }
    
    private function getQuery() {
        switch ($this->role) {
            case 'admin':
                return "SELECT nama_lengkap, jenis_kelamin, no_hp, jurusan, prodi, profesi, email 
                        FROM {$this->role} WHERE id = ?";
            case 'dosen':
                return "SELECT nama_lengkap, jenis_kelamin, no_hp, jurusan, prodi, profesi, email, pfp 
                        FROM dosen WHERE id = ?";
            case 'mahasiswa':
                return "SELECT nim, nama_lengkap, jenis_kelamin, no_hp, no_hp_ortu, 
                        jurusan, prodi, kelas, email, pfp 
                        FROM mahasiswa WHERE id = ?";
            default:
                throw new Exception('Invalid role');
        }
    }
    
    public function fetch() {
        try {
            $stmt = $this->db->prepare($this->getQuery());
            $stmt->execute([$this->userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result === false) {
                return null;
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}


class AuthenticationHandler {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function handleLogin($email, $password) {
        try {
            $user = new User($this->db);
            
            if ($user->authenticate($email, $password)) {
                $user->createSession();
                header("Location: " . $user->getRedirectPath());
                exit();
            }
            
            $_SESSION['error'] = "Email atau password salah!";
            header("Location: ../index.php");
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: ../index.php");
            exit();
        }
    }
    
    public function handleProfileFetch() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            http_response_code(401);
            echo json_encode(['error' => 'User not logged in']);
            exit();
        }
        
        try {
            $profileFetcher = new ProfileFetcher(
                $this->db, 
                $_SESSION['user_id'], 
                $_SESSION['role']
            );
            
            $data = $profileFetcher->fetch();
            
            if ($data) {
                // Ensure all numeric fields are properly formatted
                if (isset($data['no_hp'])) {
                    $data['no_hp'] = (string)$data['no_hp'];
                }
                if (isset($data['no_hp_ortu'])) {
                    $data['no_hp_ortu'] = (string)$data['no_hp_ortu'];
                }
                
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
}

// Main execution
$handler = new AuthenticationHandler($koneksi);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'fetchProfile') {
        $handler->handleProfileFetch();
    } else {
        $handler->handleLogin($_POST['email'], $_POST['password']);
    }
}
