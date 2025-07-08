<?php
$conn = new mysqli("localhost", "root", "", "kasir_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$pesan = '';
$edit_mode = false;
$edit_data = null;

// Proses hapus stok
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM produk_stok WHERE id_pembelian = $id");
    $pesan = "Data stok berhasil dihapus!";
}

// Ambil data untuk form edit
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = intval($_GET['edit']);
    $query = $conn->query("SELECT * FROM produk_stok WHERE id_pembelian = $id");
    $edit_data = $query->fetch_assoc();
}

// Proses update data
if (isset($_POST['update'])) {
    $id = intval($_POST['id']); // ini adalah id_pembelian
    $id_pembelian = $id;
    $nama = trim($_POST['nama_stok']);
    $satuan = floatval($_POST['satuan']);
    $stok = intval($_POST['stok']);

    if ($nama !== '') {
        $stmt = $conn->prepare("UPDATE produk_stok SET nama_stok=?, satuan=?, stok=? WHERE id_pembelian=?");
        $stmt->bind_param("sdii", $nama, $satuan, $stok, $id_pembelian);
        if ($stmt->execute()) {
            header("Location: data_manajemenstok.php?status=update");
            exit;
        } else {
            $pesan = "Gagal mengupdate data.";
        }
    } else {
        $pesan = "Nama stok tidak boleh kosong.";
    }
}

// Proses tambah data
if (isset($_POST['simpan'])) {
    $id_pembelian = intval($_POST['id_pembelian']);
    $nama = trim($_POST['nama_stok']);
    $satuan = floatval($_POST['satuan']);
    $stok = intval($_POST['stok']);

    if ($nama !== '') {
        $stmt = $conn->prepare("INSERT INTO produk_stok (id_pembelian, nama_stok, satuan, stok) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isdi", $id_pembelian, $nama, $satuan, $stok);
        if ($stmt->execute()) {
            header("Location: data_manajemenstok.php?status=sukses");
            exit;
        } else {
            $pesan = "Gagal menyimpan data.";
        }
    } else {
        $pesan = "Nama stok tidak boleh kosong.";
    }
}

$stok_list = $conn->query("SELECT * FROM produk_stok");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Stok Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Manajemen Stok Produk</h4>
        </div>
        <div class="card-body">
            <?php if ($pesan): ?>
                <div class="alert alert-warning"><?= $pesan ?></div>
            <?php endif; ?>

            <form method="POST">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id_pembelian'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label>ID Pembelian</label>
                    <input type="number" name="id_pembelian" class="form-control" required value="<?= $edit_mode ? $edit_data['id_pembelian'] : '' ?>">
                </div>
                <div class="mb-3">
                    <label>Nama Stok</label>
                    <input type="text" name="nama_stok" class="form-control" required value="<?= $edit_mode ? $edit_data['nama_stok'] : '' ?>">
                </div>
                <div class="mb-3">
                    <label>Harga Satuan</label>
                    <input type="number" step="any" name="satuan" class="form-control" required value="<?= $edit_mode ? $edit_data['satuan'] : '' ?>">
                </div>
                <div class="mb-3">
                    <label>Jumlah Stok</label>
                    <input type="number" name="stok" class="form-control" required value="<?= $edit_mode ? $edit_data['stok'] : '' ?>">
                </div>
                <button type="submit" name="<?= $edit_mode ? 'update' : 'simpan' ?>" class="btn btn-success">
                    <?= $edit_mode ? 'Simpan Perubahan' : 'Simpan' ?>
                </button>
                <?php if ($edit_mode): ?>
                    <a href="data_manajemenstok.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Daftar Stok Produk</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>ID Pembelian</th>
                        <th>Nama</th>
                        <th>Harga Satuan</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $stok_list->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['id_pembelian'] ?></td>
                            <td><?= $row['nama_stok'] ?></td>
                            <td>Rp <?= number_format($row['satuan'], 0, ',', '.') ?></td>
                            <td><?= $row['stok'] ?></td>
                            <td>
                                <a href="?edit=<?= $row['id_pembelian'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?hapus=<?= $row['id_pembelian'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>