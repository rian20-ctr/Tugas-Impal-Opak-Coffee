<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    if (empty($email) || empty($password) || empty($password_confirm)) {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!'); window.history.back();</script>";
        exit;
    }

    if ($password !== $password_confirm) {
        echo "<script>alert('Password tidak cocok!'); window.history.back();</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update password 
    $query = "UPDATE user_admin SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $hashed_password, $email);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<script>alert('Password berhasil diperbarui!'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui password atau email tidak ditemukan!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
