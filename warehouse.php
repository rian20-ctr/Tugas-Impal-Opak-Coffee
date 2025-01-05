<?php
session_start(); 
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

require_once 'koneksi.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['id_stok'])) {
    $id_stok = $_GET['id_stok'];
    $sql = "SELECT * FROM warehouse WHERE id_stok = $id_stok";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $row['image'] = base64_encode($row['image']);
      echo json_encode($row);
    } else {
      echo json_encode(['message' => 'Barang tidak ditemukan']);
    }
  } else {
    $sql = "SELECT * FROM warehouse";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
      $row['image'] = base64_encode($row['image']);
      $data[] = $row;
    }
    echo json_encode($data);
  }
}

// Tambah barang baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_barang'], $_POST['kategori'], $_POST['harga'], $_POST['stok_tersedia']) && !isset($_POST['id_stok'])) {
  $nama_barang = $_POST['nama_barang'];
  $kategori = $_POST['kategori'];
  $harga = $_POST['harga'];
  $stok_tersedia = $_POST['stok_tersedia'];

  //  unggahan gambar
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $gambar_tmp = $_FILES['gambar']['tmp_name'];
    $gambar_nama = $_FILES['gambar']['name'];
    $gambar_tipe = $_FILES['gambar']['type'];

    // Validasi tipe file gambar
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($gambar_tipe, $allowed_types)) {
      echo json_encode(['status' => 'error', 'message' => 'Tipe file gambar tidak valid']);
      exit;
    }

    //  isi file gambar
    $gambar_data = file_get_contents($gambar_tmp);

    // Query insert
    $sql = "INSERT INTO warehouse (nama_barang, kategori, harga, stok_tersedia, image, user_id, stok_awal) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
      // Stok_awal diisi otomatis dengan stok_tersedia saat pertama kali ditambahkan
      $stmt->bind_param("ssdisii", $nama_barang, $kategori, $harga, $stok_tersedia, $gambar_data, $user_id, $stok_tersedia);
      if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Barang baru berhasil ditambahkan']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan barang']);
      }
      $stmt->close();
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan dalam persiapan query']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Gambar tidak diunggah']);
  }
}

// Edit barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_stok'])) {
  $id_stok = $_POST['id_stok'];
  $nama_barang = $_POST['nama_barang'];
  $kategori = $_POST['kategori'];
  $harga = $_POST['harga'];
  $stok_tersedia = $_POST['stok_tersedia'];

  // select stok_awal yg udah ada sebelumnya untuk update
  $sql = "SELECT stok_tersedia, stok_awal FROM warehouse WHERE id_stok = ?";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id_stok);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($current_stok_tersedia, $current_stok_awal);
    $stmt->fetch();
    $stmt->close();
    
    // if stok_tersedia bertambah, maka  stok_awal di update dgn nilai stok_tersedia
    if ($stok_tersedia > $current_stok_tersedia) {
        $stok_awal_baru = $stok_tersedia; 
    } else {
        $stok_awal_baru = $current_stok_awal;  
    }

    // Update data barang
    $sqlUpdate = "UPDATE warehouse SET nama_barang=?, kategori=?, harga=?, stok_tersedia=?, stok_awal=?, user_id=? WHERE id_stok=?";
    if ($stmtUpdate = $conn->prepare($sqlUpdate)) {
      $stmtUpdate->bind_param("ssdisii", $nama_barang, $kategori, $harga, $stok_tersedia, $stok_awal_baru, $user_id, $id_stok);
      if ($stmtUpdate->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Barang berhasil diupdate']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate barang']);
      }
      $stmtUpdate->close();
    }
  }
}

//  Delete barang
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id_stok'])) {
  $id_stok = $_GET['id_stok'];

  $sql = "DELETE FROM warehouse WHERE id_stok=?";

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id_stok);
    if ($stmt->execute()) {
      echo json_encode(['message' => 'Barang berhasil dihapus']);
    } else {
      echo json_encode(['message' => 'Gagal menghapus barang']);
    }
    $stmt->close();
  } else {
    echo json_encode(['message' => 'Terjadi kesalahan dalam persiapan query']);
  }
}

// Stop koneksi db
$conn->close();
?>
