<?php
require 'koneksi.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_admin = mysqli_real_escape_string($conn, $_POST["nama_admin"]);
    $alamat = mysqli_real_escape_string($conn, $_POST["alamat"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    // Cek apakah email sudah terdaftar
    $checkEmailQuery = "SELECT * FROM user_admin WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email sudah terdaftar, silakan gunakan email lain.";
        exit();
    }

    // Hash password 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Upload gambar
    $image = NULL;
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image = file_get_contents($_FILES["image"]["tmp_name"]); 
    }

    // simpan password yang telah di-hash
    $stmt = $conn->prepare("INSERT INTO user_admin (nama_admin, alamat, email, password, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama_admin, $alamat, $email, $hashed_password, $image);

    if ($stmt->execute()) {
        echo "Pendaftaran Berhasil!";
        header("Location: index.html");
        exit(); 
    } else {
        echo "Pendaftaran Gagal: " . mysqli_error($conn);
    }
}
?>
