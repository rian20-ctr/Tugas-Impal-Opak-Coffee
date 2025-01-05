<?php
session_start(); //  session
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

include('koneksi.php'); 
$user_id = $_SESSION['user_id']; //  user_id dari session

//  data pesanan dr keranjang
$query = "SELECT * FROM keranjang WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

// inisiasi variabel untuk perhitungan total
$totalItem = 0;
$totalPrice = 0;
$pajak = 0;
$totalHarga = 0;
$items = [];

// ambil data dari keranjang dan menghitung total
while ($row = mysqli_fetch_assoc($result)) {
    $totalItem += $row['jumlah'];
    $totalPrice += $row['harga'] * $row['jumlah'];
    $items[] = $row; // simpan semua data untuk pemrosesan lebih lanjut
}

//  pajak 2% dari total harga
$pajak = $totalPrice * 0.02;
$totalHarga = $totalPrice + $pajak;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paymentMethod = $_POST['payment_method']; 
    $buyerName = $_POST['buyer_name']; // simpan nama pembeli dari form

    // Insert pesanan ke tabel nota
    $itemNames = implode(", ", array_column($items, 'nama_makanan')); // Gabungkan nama makanan
    $queryNota = "INSERT INTO nota (user_id, nama_makanan, jumlah_pesanan, total_harga, waktu, payment_method, nama_pembeli) 
                  VALUES ('$user_id', '$itemNames', '$totalItem', '$totalHarga', NOW(), '$paymentMethod', '$buyerName')";

    if (mysqli_query($conn, $queryNota)) {
        // Update stok di warehouse dan hapus dari keranjang
        foreach ($items as $item) {
            $idStok = $item['id_stok'];
            $jumlahDipesan = $item['jumlah'];

            $updateStokQuery = "UPDATE warehouse 
                                SET stok_tersedia = stok_tersedia - $jumlahDipesan 
                                WHERE id_stok = $idStok";
            if (!mysqli_query($conn, $updateStokQuery)) {
                echo "<script>alert('Terjadi kesalahan saat mengupdate stok di warehouse.');</script>";
                continue;
            }
            $updateStokBerkurangQuery = "UPDATE warehouse 
                                         SET stok_berkurang = stok_awal - stok_tersedia 
                                         WHERE id_stok = $idStok";
            if (!mysqli_query($conn, $updateStokBerkurangQuery)) {
                echo "<script>alert('Terjadi kesalahan saat memperbarui kolom stok_berkurang.');</script>";
            }
        }

        // Hapus data  keranjang jika pesanan berhasil
        $deleteKeranjangQuery = "DELETE FROM keranjang WHERE user_id = '$user_id'";
        mysqli_query($conn, $deleteKeranjangQuery);

        echo "<script>alert('Pesanan berhasil! arigatōgozaimasu.'); window.location.href='nota.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memproses pesanan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <link rel="stylesheet" href="css-bootstrap/bootstrap.css">
    <link rel="stylesheet" href="css-style/order.css">
    <link rel="stylesheet" href="css-style/root.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
                            <a class="nav-link mx-3" aria-current="page" href="dashboard.php">Home</a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link active  mx-3" aria-current="page" href="order.php">Order</a>
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

    <main>
        <div class="container">
            <div class="content">
                <h2 class="text-center m-3">Order</h2>
                <form method="POST" action="order.php">
                    <div class="mb-3">
                        <label for="buyer-name" class="form-label">Nama Pembeli</label>
                        <input type="text" class="form-control" id="buyer-name" name="buyer_name" required>
                    </div>

                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Makanan</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM keranjang WHERE user_id = '$user_id'";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)):
                            ?>
                            <tr>
                                <td><?php echo $row['nama_makanan']; ?></td>
                                <td>Rp. <?php echo number_format($row['harga'], 2, ',', '.'); ?></td>
                                <td>
                                    <span><?php echo $row['jumlah']; ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    </div>
                    

                    <div class="payment d-flex">
                        <div class="icon">
                            <i class="bi bi-currency-dollar"></i>
                            <p>Metode Pembayaran</p>
                        </div>
                        <div class="metode">
                            <select name="payment_method" id="payment-method" class="form-select" required>
                                <option value="" disabled selected>Pilih Metode Pembayaran</option>
                                <option value="COD">COD (Cash on Delivery)</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>
                    </div>

                    <div class="order-btn mt-3">
                        <button type="submit" class="btn btn-primary">Order</button>
                    </div>
                </form>

                <!-- Tampilan Detail Pesanan -->
                <div class="detail-pesanan">
                    <h3>Detail Pesanan</h3>
                    <div class="detail">
                        <p>Total Item: <?php echo $totalItem; ?></p>
                        <p>Pajak (2%): Rp. <?php echo number_format($pajak, 2, ',', '.'); ?></p>
                        <p>Total Harga (termasuk Pajak): Rp. <?php echo number_format($totalHarga, 2, ',', '.'); ?></p>
                        <p>Metode Pembayaran: <span id="payment-method-display">Belum dipilih</span></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/bootstrap.js"></script>
    <script src="js/popper.min.js"></script>

    <script>
        document.getElementById('payment-method').addEventListener('change', function() {
            var selectedMethod = this.value;
            document.getElementById('payment-method-display').innerText = selectedMethod || "Belum dipilih";
        });
    </script>

</body>

</html>
