<?php
session_start();
require_once 'pembelian.php';

$conn = new mysqli("localhost", "root", "", "kasir_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pembelian = new DetailPembelian($conn);

if (!isset($_SESSION['keranjang_beli'])) {
    $_SESSION['keranjang_beli'] = [];
}

if (!isset($_SESSION['data_pembelian'])) {
    $_SESSION['data_pembelian'] = [];
}

$editIndex = -1;
$editItem = null;
if (isset($_GET['edit']) && isset($_SESSION['keranjang_beli'][$_GET['edit']])) {
    $editIndex = $_GET['edit'];
    $editItem = $_SESSION['keranjang_beli'][$editIndex];
}

if (isset($_POST['update_item'])) {
    $index = $_POST['edit_index'];
    if (isset($_SESSION['keranjang_beli'][$index])) {
        $jumlah = (int)$_POST['jumlah'];
        $harga = (float)$_POST['harga'];
        $_SESSION['keranjang_beli'][$index] = [
            'id_produk' => $_POST['id_produk'],
            'nama_produk' => $_POST['nama_produk'],
            'jumlah' => $jumlah,
            'harga' => $harga,
            'subtotal' => $jumlah * $harga,
            'pajak' => ($jumlah * $harga) * 0.11
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_item'])) {
    $jumlah = (int)$_POST['jumlah'];
    $harga = (float)$_POST['harga'];

    $_SESSION['data_pembelian'] = [
        'tanggal_pembelian' => $_POST['tgl_beli'],
        'tanggal_pengiriman' => $_POST['tgl_kirim'],
        'kepada' => $_POST['kepada'],
        'alamat' => $_POST['alamat'],
        'termin' => $_POST['termin'],
        'tanggal_jatuh_tempo' => ($_POST['termin'] === 'Kredit') ? ($_POST['tgl_tempo'] ?? null) : null
    ];

    $item = [
        'id_produk' => $_POST['id_produk'],
        'nama_produk' => $_POST['nama_produk'],
        'jumlah' => $jumlah,
        'harga' => $harga,
        'subtotal' => $jumlah * $harga,
        'pajak' => ($jumlah * $harga) * 0.11
    ];
    $_SESSION['keranjang_beli'][] = $item;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['simpan_transaksi'])) {
    $items = $_SESSION['keranjang_beli'];
    $data = $_SESSION['data_pembelian'];

    $total = array_sum(array_column($items, 'subtotal'));
    $total_pajak = array_sum(array_column($items, 'pajak'));
    $total_all = $total + $total_pajak;

    $uang_dibayar = (float)$_POST['uang_dibayar'];
    $kembalian = $uang_dibayar - $total_all;

    $pembelian->setData([
        'tanggal_pembelian' => $data['tanggal_pembelian'],
        'tanggal_pengiriman' => $data['tanggal_pengiriman'],
        'kepada' => $data['kepada'],
        'alamat' => $data['alamat'],
        'termin' => $data['termin'],
        'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
        'total_pajak' => $total_pajak,
        'total_harga' => $total_all,
        'uang_dibayar' => $uang_dibayar,
        'kembalian' => $kembalian
    ]);

    $id = $pembelian->simpanTransaksi();
    $pembelian->simpanDetail($id, $items);

    unset($_SESSION['keranjang_beli']);
    unset($_SESSION['data_pembelian']);

    echo "<script>alert('Transaksi berhasil disimpan! Kembalian: Rp " . number_format($kembalian) . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $index = $_GET['hapus'];
    if (isset($_SESSION['keranjang_beli'][$index])) {
        unset($_SESSION['keranjang_beli'][$index]);
        $_SESSION['keranjang_beli'] = array_values($_SESSION['keranjang_beli']);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$keranjang = $_SESSION['keranjang_beli'];
$dataPembelian = $_SESSION['data_pembelian'];
$total = array_sum(array_column($keranjang, 'subtotal'));
$pajak = array_sum(array_column($keranjang, 'pajak'));
$totalBayar = $total + $pajak;

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    * {
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(to right, #e0eafc, #cfdef3);
        color: #333;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(20px);}
        to {opacity: 1; transform: translateY(0);}
    }

    h2 {
        margin-top: 0;
        font-weight: 700;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    form .row {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    form .row label {
        flex: 1;
        font-weight: 600;
        color: #34495e;
    }

    form .row input,
    form .row select {
        flex: 2;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f8f9fa;
        transition: border 0.3s ease;
    }

    form .row input:focus,
    form .row select:focus {
        border-color: #007bff;
        outline: none;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
        border-radius: 12px;
        overflow: hidden;
    }

    table th, table td {
        padding: 12px 14px;
        text-align: center;
    }

    table th {
        background: #007bff;
        color: white;
        text-transform: uppercase;
        font-size: 14px;
    }

    table tr:nth-child(even) {
        background: #f4f6f9;
    }

    .btn {
        padding: 10px 18px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .btn-danger {
        background-color: #e74c3c;
    }

    .btn-danger:hover {
        background-color: #c0392b;
    }

    .total-box {
        text-align: right;
        margin-top: 20px;
        background: #f1f9f9;
        padding: 15px;
        border-radius: 10px;
        box-shadow: inset 0 0 6px rgba(0,0,0,0.03);
    }

    .total-box p {
        margin: 5px 0;
        font-weight: 500;
        font-size: 16px;
    }

    .form-actions {
        margin-top: 25px;
        text-align: right;
    }

    .form-actions .btn {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        form .row {
            flex-direction: column;
        }

        .total-box {
            text-align: left;
        }
    }
</style>
</head>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputBayar = document.querySelector('input[name="uang_dibayar"]');
        const totalBayar = <?= $totalBayar ?>;
        
        const kembalianText = document.createElement('p');
        kembalianText.style.marginTop = '10px';
        kembalianText.style.fontWeight = '600';
        inputBayar.parentElement.appendChild(kembalianText);

        inputBayar.addEventListener('input', function () {
            const dibayar = parseFloat(this.value);
            if (!isNaN(dibayar) && dibayar >= totalBayar) {
                const kembali = dibayar - totalBayar;
                kembalianText.textContent = 'Uang Kembali: Rp ' + kembali.toLocaleString('id-ID');
                kembalianText.style.color = '#28a745'; // hijau
            } else {
                kembalianText.textContent = '';
            }
        });
    });
</script>
<body>
<div class="container">
    <h2>Form Transaksi Pembelian</h2>

    <!-- Form Tambah Item -->
    <form method="POST">
        <div class="row">
            <label>Tanggal Pembelian</label>
            <input type="date" name="tgl_beli" required value="<?= $dataPembelian['tanggal_pembelian'] ?? '' ?>">
        </div>
        <div class="row">
            <label>Tanggal Pengiriman</label>
            <input type="date" name="tgl_kirim" required value="<?= $dataPembelian['tanggal_pengiriman'] ?? '' ?>">
        </div>
        <div class="row">
            <label>Supplier / Kepada</label>
            <input type="text" name="kepada" required value="<?= $dataPembelian['kepada'] ?? '' ?>">
        </div>
        <div class="row">
            <label>Alamat</label>
            <input type="text" name="alamat" required value="<?= $dataPembelian['alamat'] ?? '' ?>">
        </div>
        <div class="row">
            <label>Termin</label>
            <select name="termin" required onchange="document.getElementById('jatuh_tempo').style.display = this.value === 'Kredit' ? 'block' : 'none'">
                <option value="Tunai" <?= (isset($dataPembelian['termin']) && $dataPembelian['termin'] === 'Tunai') ? 'selected' : '' ?>>Tunai</option>
                <option value="Kredit" <?= (isset($dataPembelian['termin']) && $dataPembelian['termin'] === 'Kredit') ? 'selected' : '' ?>>Kredit</option>
            </select>
        </div>
        <div class="row" id="jatuh_tempo" style="display: <?= (isset($dataPembelian['termin']) && $dataPembelian['termin'] === 'Kredit') ? 'block' : 'none' ?>">
            <label>Tanggal Jatuh Tempo</label>
            <input type="date" name="tgl_tempo" value="<?= $dataPembelian['tanggal_jatuh_tempo'] ?? '' ?>">
        </div>

        <hr style="margin: 20px 0;">

        <h2><?= $editItem ? "Edit Produk" : "Tambah Produk" ?></h2>
        <input type="hidden" name="edit_index" value="<?= $editIndex ?>">
        <div class="row">
            <label>ID Produk</label>
            <input type="text" name="id_produk" required value="<?= $editItem['id_produk'] ?? '' ?>">
        </div>
        <div class="row">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" required value="<?= $editItem['nama_produk'] ?? '' ?>">
        </div>
        <div class="row">
            <label>Jumlah</label>
            <input type="number" name="jumlah" required value="<?= $editItem['jumlah'] ?? 1 ?>">
        </div>
        <div class="row">
            <label>Harga</label>
            <input type="number" step="0.01" name="harga" required value="<?= $editItem['harga'] ?? 0 ?>">
        </div>

        <div class="form-actions">
            <?php if ($editItem): ?>
                <button type="submit" name="update_item" class="btn">Update Produk</button>
            <?php else: ?>
                <button type="submit" name="tambah_item" class="btn">Tambah ke Keranjang</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Tabel Keranjang -->
    <?php if (count($keranjang) > 0): ?>
        <h2 style="margin-top: 40px;">Keranjang Pembelian</h2>
        <table>
            <thead>
            <tr>
                <th>ID Produk</th>
                <th>Nama</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Pajak</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($keranjang as $i => $item): ?>
                <tr>
                    <td><?= $item['id_produk'] ?></td>
                    <td><?= $item['nama_produk'] ?></td>
                    <td><?= $item['jumlah'] ?></td>
                    <td>Rp <?= number_format($item['harga']) ?></td>
                    <td>Rp <?= number_format($item['subtotal']) ?></td>
                    <td>Rp <?= number_format($item['pajak']) ?></td>
                    <td>
                        <a href="?edit=<?= $i ?>" class="btn">Edit</a>
                        <a href="?hapus=<?= $i ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus item ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-box">
            <p>Total: <strong>Rp <?= number_format($total) ?></strong></p>
            <p>Pajak 11%: <strong>Rp <?= number_format($pajak) ?></strong></p>
            <p>Total Bayar: <strong>Rp <?= number_format($totalBayar) ?></strong></p>
        </div>

        <form method="POST">
            <div class="row">
                <label>Uang Dibayar</label>
                <input type="number" name="uang_dibayar" step="0.01" required>
            </div>
            <div class="form-actions">
                <button type="submit" name="simpan_transaksi" class="btn">Simpan Transaksi</button>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>