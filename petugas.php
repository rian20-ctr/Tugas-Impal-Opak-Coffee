<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

require 'koneksi.php';

//  DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_admin'])) {
    $id = intval($_POST['id_admin']);
    $sql = "DELETE FROM user_admin WHERE id_admin = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

//  UPDATE request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_edit'])) {
    $id = intval($_POST['id_edit']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if (empty($nama) || empty($alamat) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    $sql = "UPDATE user_admin SET nama_admin = ?, alamat = ?, email = ? WHERE id_admin = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $alamat, $email, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil diperbarui']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data']);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

// Fetch data
$sql = "SELECT id_admin, nama_admin, email, alamat, image FROM user_admin";
$result = mysqli_query($conn, $sql);

$petugas = [];
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['image']) {
        $row['image'] = 'data:image/jpeg;base64,' . base64_encode($row['image']);
    } else {
        $row['image'] = null;
    }
    $petugas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Petugas</title>
    <link rel="stylesheet" href="css-bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css-style/warehouse-style.css">
    <link rel="stylesheet" href="css-style/root.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <style>
        .petugas-table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .petugas-table td {
            vertical-align: middle;
        }
    </style>
</head>

<body id="warehouse" style="background-image: url(Asset/background/dark_wood.png);">
    <div class="nav-bar">
        <nav class="navbar navbar-expand-lg navbar-dark" data-bs-theme="dark">
            <div class="container">
                <a class="navbar-brand fw-bold fs-2" href="dashboard.php" style="font-family: Oswald, sans-serif;">OpakCoffee</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-center">
                        <li class="nav-item"><a class="nav-link mx-3" href="dashboard.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="order.php">Order</a></li>
                        <li class="nav-item"><a class="nav-link mx-3" href="warehouse.html">Warehouse</a></li>
                        <li class="nav-item"><a class="nav-link active mx-3" href="#petugas">Petugas</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-bs-toggle="dropdown">Account</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="account.php">Account</a></li>
                                <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                            </ul>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <header>
            <h1 class="text-center text-light mt-5 mb-3">Daftar Petugas</h1>
        </header>
        <div class="petugas-table">
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Foto</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Email</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($petugas)): ?>
                            <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($petugas as $row): ?>
                                <tr>
                                    <td><img src="<?= $row['image'] ?: 'path/to/default-image.jpg' ?>" alt="Foto"></td>
                                    <td><?= htmlspecialchars($row['nama_admin']) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick="hapusPetugas(<?= $row['id_admin'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.js"></script>
    <script>
        function hapusPetugas(id) {
            fetch('', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `id_admin=${id}` })
                .then(response => response.json())
                .then(data => { alert(data.message); location.reload(); });
        }
    </script>
</body>

</html>
