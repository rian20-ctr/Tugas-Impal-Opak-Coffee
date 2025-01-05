<?php
session_start(); 
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}
include('koneksi.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Opak Coffee</title>
  <link rel="stylesheet" href="css-bootstrap/bootstrap.css">
  <link rel="stylesheet" href="css-style/dashboard-style.css">
  <link rel="stylesheet" href="css-style/root.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body id="home" style="background-image: url(Asset/background/dark_wood.png);">

  <!-- Navbar -->
  <div class="nav-bar">
    <nav class="navbar navbar-expand-lg navbar-dark" data-bs-theme="dark">
      <div class="container">
        <a class="navbar-brand fw-bold fs-2" href="dashboard.php" style="font-family: Oswald, sans-serif;">OpakCoffee</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-center">
            <li class="nav-item"><a class="nav-link active mx-3" href="#home" style="color: rgb(200, 130, 46);font-weight: bold;">Home</a></li>
            <li class="nav-item"><a class="nav-link mx-3" href="order.php">Order</a></li>
            <li class="nav-item"><a class="nav-link mx-3" href="warehouse.html">Warehouse</a></li>
            <li class="nav-item"><a class="nav-link mx-3" href="petugas.php">Petugas</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">Account</a>
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
  <!-- End Navbar -->

  <!-- Rekomendasi -->
  <div class="recomendasi">
    <div class="container py-5">
      <h2 class="recommend-title">Rekomendasi</h2>
      <div class="row g-4 d-flex justify-content-center">
        <?php
        // Tampilkan barang dengan stok_berkurang terbanyak
        $sql = "SELECT * FROM warehouse ORDER BY stok_berkurang DESC LIMIT 6";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
        ?>
          <div class="col-4 col-sm-3 col-lg-2">
            <div class="recommend-card">
              <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" alt="<?php echo $row['nama_barang']; ?>">
              <h5><?php echo $row['nama_barang']; ?></h5>
              <p><?php echo "Rp " . number_format($row['harga'], 2, ',', '.'); ?></p>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <!-- End Rekomendasi -->

  <!-- Makanan dan Minuman -->
  <div class="makanan-dan-minuman">
    <div class="container py-5">
      <h2 class="makanan-dan-minuman-title mb-3">Makanan dan Minuman</h2>
      <h3 class="all-menu d-flex justify-content-end">
        <a href="all-food.php" class="btn btn-outline-light btn-lg rounded-1 btn-2 mt-1 mb-4">Pesan & Lihat Semua</a>
      </h3>
      <div class="row g-4 d-flex justify-content-center">
        <?php
        $sqlCombined = "SELECT * FROM warehouse WHERE kategori IN ('makanan', 'minuman') LIMIT 6";
        $resultCombined = mysqli_query($conn, $sqlCombined);

        while ($rowCombined = mysqli_fetch_assoc($resultCombined)) {
        ?>
          <div class="col-4 col-sm-3 col-lg-2">
            <div class="makanan-dan-minuman-card">
              <img src="data:image/jpeg;base64,<?php echo base64_encode($rowCombined['image']); ?>" alt="<?php echo $rowCombined['nama_barang']; ?>">
              <h5 class="text-center"><?php echo $rowCombined['nama_barang']; ?></h5>
              <p class="text-center"><?php echo "Rp " . number_format($rowCombined['harga'], 2, ',', '.'); ?></p>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <!-- End Makanan dan Minuman -->

  <!-- Footer -->
  <footer>
    <p class="text-center">©2024 OpakCoffee. All Rights Reserved</p>
  </footer>

  <script src="js/bootstrap.js"></script>
  <script src="js-bootstrap/bootstrap.bundle.js"></script>

</body>
</html>
