<?php
session_start(); // Start session
header('Content-Type: text/html; charset=UTF-8');
require 'koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html'); 
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle UPDATE request
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

// Fetch user data based on ID
$sql = "SELECT id_admin, nama_admin, email, alamat, image FROM user_admin WHERE id_admin = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$petugas = mysqli_fetch_assoc($result);

if (!$petugas) {
    echo "Data tidak ditemukan!";
    exit;
}

// Convert image to base64 if exists
if ($petugas['image']) {
    $petugas['image'] = 'data:image/jpeg;base64,' . base64_encode($petugas['image']);
} else {
    $petugas['image'] = 'Asset/team/default-profile.jpg';
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css-bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css-style/account.css">
    <link rel="stylesheet" href="css-style/root.css">

    <!-- Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body id="about" style="background-image: url(Asset/background/dark_wood.png);">
    <!-- Navbar -->
    <div class="nav-bar">
        <nav class="navbar navbar-expand-lg  navbar-dark" data-bs-theme="dark">
            <div class="container">
                <a class="navbar-brand fw-bold fs-2" href="dashboard.php" style="font-family: Oswald, sans-serif;">OpakCoffee</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon "></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-center">
                        <li class="nav-item ">
                            <a class="nav-link mx-3" aria-current="page" href="dashboard.php">Home</a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link  mx-3" aria-current="page" href="order.php">Order</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link  mx-3" aria-current="page" href="warehouse.html">Warehouse</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link  mx-3" aria-current="page" href="petugas.php">Petugas</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link active dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Account</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="account.php">Account</a></li>
                                <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <!-- End Navbar -->

    <!-- Content -->
    <main>
        <div class="container">
            <div class="account-content">
                <div class="image-wrapper">
                    <img id="profile-image" src="<?php echo $petugas['image']; ?>" alt="Profile Image" width="250px" height="250px" style="object-fit:cover;">
                </div>
                <div class="account-info">
                    <table>
                        <tr>
                            <th>Nama</th>
                            <td class="dot">:</td>
                            <td id="nama"><?php echo $petugas['nama_admin']; ?></td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td class="dot">:</td>
                            <td id="alamat"><?php echo $petugas['alamat']; ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td class="dot">:</td>
                            <td id="email"><?php echo $petugas['email']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Edit Button -->
            <div class="d-flex justify-content-end">
                <button id="editButton" class="btn btn-outline-light btn-lg rounded-1 btn-2 mt-3" data-bs-toggle="modal" data-bs-target="#editModal"
                    onclick="editAccountData()">
                    Edit
                </button>
            </div>
        </div>

        <!-- Modal Edit Data -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Edit Account Information</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAccount" method="POST" action="" onsubmit="submitEdit(event)">
                            <div class="mb-3">
                                <label for="editNama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="editNama"
                                    value="<?php echo $petugas['nama_admin']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="editAlamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="editAlamat"
                                    value="<?php echo $petugas['alamat']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail"
                                    value="<?php echo $petugas['email']; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="js/bootstrap.js"></script>
    <script src="js/popper.min.js"></script>

    <script>
        let currentUserId = <?php echo $petugas['id_admin']; ?>; 

        // Function to populate the edit form
        function editAccountData() {
            document.getElementById('editNama').value = document.getElementById('nama').textContent;
            document.getElementById('editAlamat').value = document.getElementById('alamat').textContent;
            document.getElementById('editEmail').value = document.getElementById('email').textContent;
        }

        // Function to submit edited data
        function submitEdit(event) {
            event.preventDefault();

            const nama = document.getElementById('editNama').value;
            const alamat = document.getElementById('editAlamat').value;
            const email = document.getElementById('editEmail').value;

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id_edit': currentUserId,
                    'nama': nama,
                    'alamat': alamat,
                    'email': email
                })
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        document.getElementById('nama').textContent = nama;
                        document.getElementById('alamat').textContent = alamat;
                        document.getElementById('email').textContent = email;
                        var modal = document.getElementById('editModal');
                        var modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();
                    }
                })
                .catch(error => {
                    alert('Failed to update profile');
                });
        }
    </script>
</body>

</html>