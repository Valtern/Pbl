<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        
        // Common fields for all roles
        $common_fields = [
            'nama_lengkap' => $_POST['nama_lengkap'],
            'jenis_kelamin' => $_POST['jenis_kelamin'],
            'no_hp' => $_POST['no_hp'],
            'email' => $_POST['email']
        ];
        
        // Build query based on role
        switch($role) {
            case 'mahasiswa':
                $query = "UPDATE mahasiswa SET 
                            nama_lengkap = :nama_lengkap,
                            jenis_kelamin = :jenis_kelamin,
                            no_hp = :no_hp,
                            no_hp_ortu = :no_hp_ortu,
                            email = :email
                         WHERE id = :user_id";
                         
                $params = array_merge($common_fields, [
                    'no_hp_ortu' => $_POST['no_hp_ortu'],
                    'user_id' => $user_id
                ]);
                break;
                
            case 'dosen':
                $query = "UPDATE dosen SET 
                            nama_lengkap = :nama_lengkap,
                            jenis_kelamin = :jenis_kelamin,
                            no_hp = :no_hp,
                            jurusan = :jurusan,
                            prodi = :prodi,
                            profesi = :profesi,
                            email = :email
                         WHERE id = :user_id";
                         
                $params = array_merge($common_fields, [
                    'jurusan' => $_POST['jurusan'],
                    'prodi' => $_POST['prodi'],
                    'profesi' => $_POST['profesi'],
                    'user_id' => $user_id
                ]);
                break;
                
            case 'admin':
                $query = "UPDATE admin SET 
                            nama_lengkap = :nama_lengkap,
                            jenis_kelamin = :jenis_kelamin,
                            no_hp = :no_hp,
                            jurusan = :jurusan,
                            prodi = :prodi,
                            profesi = :profesi,
                            email = :email
                         WHERE id = :user_id";
                         
                $params = array_merge($common_fields, [
                    'jurusan' => $_POST['jurusan'],
                    'prodi' => $_POST['prodi'],
                    'profesi' => $_POST['profesi'],
                    'user_id' => $user_id
                ]);
                break;
                
            default:
                throw new Exception("Invalid user role");
        }
        
        $stmt = $koneksi->prepare($query);
        
        if ($stmt->execute($params)) {
            $_SESSION['success_message'] = "Profile updated successfully";
        } else {
            $_SESSION['error_message'] = "Failed to update profile";
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header("Location: ../{$role}/dashboard.php");
    exit();
}
