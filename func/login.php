<?php
session_start();
require_once '../connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'fetchProfile') {
        // Fetch user profile logic
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'User not logged in']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        try {
            $query = "";
            switch ($role) {
                case 'admin':
                    $query = "SELECT nama_lengkap, jenis_kelamin, no_hp, jurusan, prodi, profesi, email FROM admin WHERE id = ?";
                    break;
                case 'dosen':
                    $query = "SELECT nama_lengkap, jenis_kelamin, no_hp, jurusan, prodi, profesi, email FROM dosen WHERE id = ?";
                    break;
                case 'mahasiswa':
                    $query = "SELECT nim, nama_lengkap, jenis_kelamin, no_hp, no_hp_ortu, jurusan, prodi, kelas, email FROM mahasiswa WHERE id = ?";
                    break;
            }

            $stmt = $koneksi->prepare($query);
            $stmt->execute([$user_id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        echo "Debug: Email received: $email<br>"; // Debug: Ensure email is received
        echo "Debug: Password received: $password<br>"; // Debug: Ensure password is received

        // Check in mahasiswa table
        $query = "SELECT id, nama_lengkap, 'mahasiswa' as role FROM mahasiswa WHERE email = ? AND password = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo "Debug: Found user in 'mahasiswa' table.<br>";
        } else {
            echo "Debug: Not found in 'mahasiswa' table.<br>";

            // Check in admin table
            $query = "SELECT id, nama_lengkap, 'admin' as role FROM admin WHERE email = ? AND password = ?";
            $stmt = $koneksi->prepare($query);
            $stmt->execute([$email, $password]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo "Debug: Found user in 'admin' table.<br>";
            } else {
                echo "Debug: Not found in 'admin' table.<br>";

                // Check in dosen table
                $query = "SELECT id, nama_lengkap, 'dosen' as role FROM dosen WHERE email = ? AND password = ?";
                $stmt = $koneksi->prepare($query);
                $stmt->execute([$email, $password]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    echo "Debug: Found user in 'dosen' table.<br>";
                } else {
                    echo "Debug: Not found in 'dosen' table.<br>";
                }
            }
        }

        if ($user) {
            echo "Debug: Login successful. User Role: {$user['role']}<br>";

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch($user['role']) {
                case 'admin':
                    echo "Redirecting to: ../admin/dashboard.php<br>";
                    header("Location: ../admin/dashboard.php");
                    break;
                case 'dosen':
                    echo "Redirecting to: ../dosen/dashboard.php<br>";
                    header("Location: ../dosen/dashboard.php");
                    break;
                case 'mahasiswa':
                    echo "Redirecting to: ../mahasiswa/dashboard.php<br>";
                    header("Location: ../mahasiswa/dashboard.php");
                    break;
            }
            exit();
        } else {
            echo "Debug: Login failed. Invalid email or password.<br>";
            $_SESSION['error'] = "Email atau password salah!";
            header("Location: ../index.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Debug: Database error: " . $e->getMessage() . "<br>";
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../index.php");
        exit();
    }
}
