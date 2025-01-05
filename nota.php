<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

include('koneksi.php');
$user_id = $_SESSION['user_id'];

// select nama kasir dr user_id 
$queryUser = "SELECT nama_admin FROM user_admin WHERE id_admin = '$user_id'";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);
$namaKasir = $user['nama_admin']; // save nama kasir

// select data nota dr user_id  untuk mendapatkan nama pembeli
$queryNota = "SELECT * FROM nota WHERE user_id = '$user_id' ORDER BY waktu DESC LIMIT 1"; // ambil nota terakhir
$resultNota = mysqli_query($conn, $queryNota);
$nota = mysqli_fetch_assoc($resultNota);

//  apakah ada data nota??
if ($nota) {
    $items = explode(", ", $nota['nama_makanan']); // dipisah nama makanan yang dipesan
    $totalHarga = $nota['total_harga'];
    $pajak = $totalHarga * 0.02;
    $namaPembeli = $nota['nama_pembeli']; // nama pembeli diambil dari tabel nota
} else {
    echo "Nota tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pesanan</title>
    <link rel="stylesheet" href="css-bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css-style/order.css">
    <link rel="stylesheet" href="css-style/nota.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>



<body style="background-image: url(Asset/background/dark_wood.png);">

    <main>
        <div class="container">
            <div class="content">

                <header>
                    <div class="logo-nota">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h2>Pesanan Berhasil</h2>
                    <p>Transaksi Anda telah berhasil</p>
                    <p>Pesanan Anda akan segera diantar!</p>
                </header>
                <hr>

                <div class="nota-detail">
                    <h4 style=" color: rgb(79, 61, 38);">Detail Pesanan</h4>
                    <div class="info-nota nama-kasir">
                        <p>Nama Kasir:</p>
                        <p><?php echo $namaKasir; ?></p>
                    </div>
                    <div class="info-nota">
                        <p>Nama Pembeli:</p>
                        <p><?php echo $namaPembeli; ?></p>
                    </div>
                    <div class="info-nota">
                        <p>Waktu Pesanan:</p>
                        <p><?php echo $nota['waktu']; ?></p>
                    </div>
                    <div class="info-nota">
                        <p>Metode Pembayaran:</p>
                        <p> <?php echo $nota['payment_method']; ?></p>
                    </div>
                    <div class="info-nota">
                        <p>Nama Makanan:</p>
                        <ul style="list-style:none;">
                            <?php foreach ($items as $item): ?>
                                <li class="d-flex justify-content-end"><?php echo $item; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="info-nota">
                        <p>Total Harga:</p>
                        <p> Rp.<?php echo number_format($totalHarga, 2, ',', '.'); ?></p>
                    </div>
                    <div class="info-nota">
                        <p>Pajak (2%):</p>
                        <p> Rp.<?php echo number_format($pajak, 2, ',', '.'); ?></p>
                    </div>
                    <div class="info-nota">
                        <p><strong>Total Harga (termasuk Pajak):</strong></p>
                        <p> Rp.<?php echo number_format($totalHarga + $pajak, 2, ',', '.'); ?></p>
                    </div>
                </div>

                <div class="order-btn mt-3 mb-3">
                    <a href="all-food.php" class="btn" style="background-color: #8B4513; color: white;">Pesan Lagi</a>
                </div>
            </div>
        </div>
    </main>

    <script src="js/bootstrap.js"></script>
    <script src="js/popper.min.js"></script>

</body>

</html>