<?php
session_start(); // session
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

include 'koneksi.php';
$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['id_keranjang'])) {
    $_SESSION['id_keranjang'] = uniqid('keranjang_', true);
}

// tambah item ke keranjang 
if (isset($_POST['add'])) {
    $id_stok = $_POST['id_stok'];
    $harga = $_POST['harga'];
    $nama_makanan = $_POST['nama_makanan'];
    $id_keranjang = $_SESSION['id_keranjang'];

    // cek stok di warehouse
    $cek_stok_query = "SELECT stok_tersedia FROM warehouse WHERE id_stok = '$id_stok'";
    $cek_stok_result = mysqli_query($conn, $cek_stok_query);
    $cek_stok = mysqli_fetch_assoc($cek_stok_result)['stok_tersedia'];

    // pop up jika stok abis
    if ($cek_stok <= 0) {
        echo "<script>alert('Stok barang ini habis! Tidak dapat menambahkan ke keranjang.');</script>";
    } else {
        // cek udh ada item dengan id_stok yang sama ??
        if (isset($_SESSION['keranjang'][$id_keranjang][$id_stok])) {
            $_SESSION['keranjang'][$id_keranjang][$id_stok]['jumlah']++;
        } else {
            $_SESSION['keranjang'][$id_keranjang][$id_stok] = [
                'jumlah' => 1,
                'harga' => $harga,
                'nama_makanan' => $nama_makanan
            ];
        }
    }
}

// pengungaran item dari keranjang
if (isset($_POST['subtract'])) {
    $id_stok = $_POST['id_stok'];
    $id_keranjang = $_SESSION['id_keranjang'];

    if (isset($_SESSION['keranjang'][$id_keranjang][$id_stok])) {
        $_SESSION['keranjang'][$id_keranjang][$id_stok]['jumlah']--;

        // kika barang =0 , -> dihapus
        if ($_SESSION['keranjang'][$id_keranjang][$id_stok]['jumlah'] <= 0) {
            unset($_SESSION['keranjang'][$id_keranjang][$id_stok]);
        }
    }

    // update databaseny
    $query = "UPDATE keranjang SET jumlah = jumlah - 1 
              WHERE user_id = '$user_id' AND id_keranjang = '$id_keranjang' AND id_stok = '$id_stok'";
    mysqli_query($conn, $query);
}

// Tambah semua item ke tabel keranjang 
if (isset($_POST['add_to_cart'])) {
    $id_keranjang = $_SESSION['id_keranjang'];

    // Loop untuk memasukkan item dari session ke database
    foreach ($_SESSION['keranjang'][$id_keranjang] as $id_stok => $item) {
        $jumlah = $item['jumlah'];
        $harga = $item['harga'];
        $nama_makanan = $item['nama_makanan'];

        // Periksa apakah barang sudah ada di keranjang dalam database
        $query = "INSERT INTO keranjang (user_id, id_keranjang, id_stok, nama_makanan, harga, jumlah) 
                  VALUES ('$user_id', '$id_keranjang', '$id_stok', '$nama_makanan', '$harga', '$jumlah')
                  ON DUPLICATE KEY UPDATE jumlah = jumlah + $jumlah";
        mysqli_query($conn, $query);
    }

    // Kosongkan keranjang setelah ditambahkan ke database
    unset($_SESSION['keranjang'][$id_keranjang]);

    // Redirect ke halaman order.php setelah menambahkan ke keranjang
    header("Location: order.php");
    exit();
}

// Total jumlah barang & harga
$totalJumlah = 0;
$totalHarga = 0;

if (isset($_SESSION['keranjang'][$_SESSION['id_keranjang']])) {
    foreach ($_SESSION['keranjang'][$_SESSION['id_keranjang']] as $item) {
        $totalJumlah += $item['jumlah'];
        $totalHarga += $item['jumlah'] * $item['harga'];
    }
}

// Cek filter kategori
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';

// Query untuk mengambil data makanan dan minuman
$query = "SELECT * FROM warehouse";
if ($kategori_filter != 'all') {
    $query .= " WHERE kategori = '$kategori_filter'";
}
$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpakCoffee - All Food and Drinks</title>
    <link rel="stylesheet" href="css-bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css-style/allfood-sstyle.css">
    <link rel="stylesheet" href="css-style/root.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <!-- Font Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

</head>

<body style="background-image: url(Asset/background/dark_wood.png);">
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
                            <a class="nav-link active mx-3" aria-current="page" href="dashboard.php"
                                style="color: rgb(200, 130, 46);font-weight: bold;">Home</a>
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
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
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
        <div class="container content">
            <div class="back">
                <a href="dashboard.php" class="text-dark"><i class="bi bi-arrow-left-circle fs-2"></i></a>
            </div>

            <!-- Fitur Filter -->
            <div class="filter mb-4">
                <form action="" method="get">
                    <select class="form-select" name="kategori" onchange="this.form.submit()">
                        <option value="all" <?php echo ($kategori_filter == 'all') ? 'selected' : ''; ?>>Semua</option>
                        <option value="makanan" <?php echo ($kategori_filter == 'makanan') ? 'selected' : ''; ?>>Makanan
                        </option>
                        <option value="minuman" <?php echo ($kategori_filter == 'minuman') ? 'selected' : ''; ?>>Minuman
                        </option>
                    </select>
                </form>
            </div>

            <!--  Scroll tabel -->
            <div class="table-container pb-5" style="margin-bottom: 100px;">
                <table class="table table-hover text-center mt-4">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Foto</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td class="data"><?php echo $row['id_stok']; ?></td>
                                <td class="data"><?php echo $row['nama_barang']; ?></td>
                                <td class="data">Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                </td>
                                <td class="image">
                                    <?php
                                    if ($row['image']) {
                                        echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" >';
                                    } else {
                                        echo '<img src="Asset/food-images/default.jpg" width="100px" alt="Default Image">';
                                    }
                                    ?>
                                </td>
                                <td class="data"><?php echo ucfirst($row['kategori']); ?></td>
                                <td class="data">
                                    <!--  kurang (-) -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_stok" value="<?php echo $row['id_stok']; ?>">
                                        <button class="btn btn-dark btn-sm" name="subtract">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                    </form>

                                    <!--  tambah (+) -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_stok" value="<?php echo $row['id_stok']; ?>">
                                        <input type="hidden" name="harga" value="<?php echo $row['harga']; ?>">
                                        <input type="hidden" name="nama_makanan" value="<?php echo $row['nama_barang']; ?>">
                                        <button class="btn btn-dark btn-sm" name="add">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Keranjang -->
        <div class="keranjang fixed-bottom bg-dark text-white p-3">
            <table class="w-100">
                <tr>
                    <td>Keranjang </td>
                    <td class="jumlah-pesanan"><?php echo $totalJumlah; ?></td>
                    <td style="padding-left: 10px;">pesanan</td>
                    <td class="total-harga">Rp. <?php echo number_format($totalHarga, 0, ',', '.'); ?></td>
                </tr>
            </table>
            <!-- post dari keranjang ke db dan redirect ke order.php -->
            <form method="POST">
            <button type="submit" name="add_to_cart" class="btn btn-warning w-100 mt-2" <?php if ($totalJumlah == 0) echo "disabled"; ?>> Pesan Sekarang</button>
            </form>
        </div>
    </main>

    <script src="js/bootstrap.js"></script>
    <script src="js/popper.min.js"></script>
</body>

</html>