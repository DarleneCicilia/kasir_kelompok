<?php
session_start();
include 'db_config.php';

// Tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $satuan = $_POST['satuan'];
    
    $koneksi->query("INSERT INTO stok_barang (nama_barang, jumlah, satuan) VALUES ('$nama', '$jumlah', '$satuan')");
    header("Location: stok.php");
}

// Edit data
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $satuan = $_POST['satuan'];

    $koneksi->query("UPDATE stok_barang SET nama_barang='$nama', jumlah='$jumlah', satuan='$satuan' WHERE id='$id'");
    header("Location: stok.php");
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM stok_barang WHERE id='$id'");
    header("Location: stok.php");
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$totalProducts = count($_SESSION['cart']);
$totalValue = 0;
$totalWeight = 0;

foreach ($_SESSION['cart'] as $item) {
    $product = unserialize($item);
    $totalValue += $product->getPrice() * $product->getQuantity();
    $totalWeight += $product->getWeight() * $product->getQuantity();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Toko</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 30px;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            max-width: 1200px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .info {
            text-align: center;
            margin-bottom: 30px;
        }
        .info p {
            font-size: 18px;
            margin: 5px 0;
        }
        .main-content {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .form-section, .table-section {
            flex: 1;
            min-width: 450px;
        }
        form input, form button {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
        }
        form button {
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        .actions {
            margin-top: 30px;
            text-align: center;
        }
        .actions a {
            display: inline-block;
            margin: 5px 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        .edit-form input {
            width: 100px;
            margin: 2px;
        }
        .edit-form button {
            margin-top: 5px;
        }
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📊 Manajem stok </h1>

    <div class="info">
        <p><strong>Total Produk di Kasir:</strong> <?= $totalProducts ?> item</p>
        <p><strong>Total Nilai Belanja:</strong> Rp<?= number_format($totalValue, 0, ',', '.') ?></p>
        <p><strong>Total Berat Barang:</strong> <?= number_format($totalWeight, 2) ?> kg</p>
    </div>

    <div class="main-content">
        <!-- Form Tambah -->
        <div class="form-section">
            <h3>➕ Tambah Barang ke Stok</h3>
            <form method="post">
                <input type="text" name="nama_barang" placeholder="Nama Barang" required>
                <input type="number" name="jumlah" placeholder="Jumlah" required>
                <input type="text" name="satuan" placeholder="Satuan" required>
                <button type="submit" name="tambah">Tambah</button>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="table-section">
            <h3>📦 Data Stok Barang</h3>
            <table>
                <tr>
                    <th>No</th><th>Nama Barang</th><th>Jumlah</th><th>Satuan</th><th>Aksi</th>
                </tr>
                <?php
                $no = 1;
                $data = $conn->query("SELECT * FROM stok_barang");
                while ($row = $data->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama_barang'] ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td><?= $row['satuan'] ?></td>
                        <td>
                            <form method="post" class="edit-form" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="text" name="nama_barang" value="<?= $row['nama_barang'] ?>" required>
                                <input type="number" name="jumlah" value="<?= $row['jumlah'] ?>" required>
                                <input type="text" name="satuan" value="<?= $row['satuan'] ?>" required>
                                <button type="submit" name="edit">Edit</button>
                            </form>
                            <br>
                            <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin mau hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <div class="actions">
        <a href="index.php">🔙 Kembali ke Kasir</a>
        <a href="stok.php">📦 Manajemen Stok</a>
    </div>
</div>
</body>
</html>