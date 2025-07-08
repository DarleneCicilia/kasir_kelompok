<?php
include_once 'Supplier.php';

$conn = new mysqli("localhost", "root", "", "kasir_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$pesan = '';
$edit_mode = false;
$edit_data = null;

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM supplier WHERE id = $id");
    $pesan = "Data berhasil dihapus!";
}

// Proses ambil data untuk diedit
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = intval($_GET['edit']);
    $query = $conn->query("SELECT * FROM supplier WHERE id = $id");
    $edit_data = $query->fetch_assoc();
}

// Proses update data
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $telp = trim($_POST['telp']);
    $produk_fokus = trim($_POST['produk_fokus']);
    $diskon = isset($_POST['is_premium']) ? intval($_POST['diskon']) : 0;

    if ($nama !== '') {
        $stmt = $conn->prepare("UPDATE supplier SET nama=?, alamat=?, no_telp=?, produk_fokus=?, diskon=? WHERE id=?");
        $stmt->bind_param("ssssii", $nama, $alamat, $telp, $produk_fokus, $diskon, $id);
        if ($stmt->execute()) {
            header("Location: data_supplier.php?status=update");
            exit;
        } else {
            $pesan = "Gagal mengupdate data.";
        }
    } else {
        $pesan = "Nama tidak boleh kosong.";
    }
}

// Proses simpan data baru
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $telp = trim($_POST['telp']);
    $diskon = isset($_POST['is_premium']) ? intval($_POST['diskon']) : 0;
    $produk_fokus = trim($_POST['produk_fokus']);

    if ($nama !== '') {
        if ($diskon > 0) {
            $supplier = new SupplierPremium($nama, $alamat, $telp, $diskon);
        } else {
            $supplier = new Supplier($nama, $alamat, $telp);
        }

        // ✅ Di sini simpan nama ke variabel sebelum bind_param
        $nama_supplier = $supplier->getNama();
        $stmt = $conn->prepare("INSERT INTO supplier (nama, alamat, no_telp, diskon, produk_fokus) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $nama_supplier, $alamat, $telp, $diskon, $produk_fokus);

        if ($stmt->execute()) {
            header("Location: data_supplier.php?status=sukses");
            exit;
        } else {
            $pesan = "Gagal menyimpan data.";
        }
        } else {
        $pesan = "Nama tidak boleh kosong.";
        }
}

// Ambil semua data untuk ditampilkan
$data = $conn->query("SELECT * FROM supplier");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?= $edit_mode ? "Edit Supplier" : "Tambah Supplier" ?></h4>
        </div>
        <div class="card-body">

            <?php if (isset($_GET['status']) || $pesan): ?>
    <div class="alert 
        <?php 
            if (isset($_GET['status']) && ($_GET['status'] === 'sukses' || $_GET['status'] === 'update' || $_GET['status'] === 'hapus')) {
                echo 'alert-success';
            } else {
                echo 'alert-danger';
            }
        ?>">
        <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] === 'sukses') {
                    echo "Data berhasil disimpan!";
                } elseif ($_GET['status'] === 'update') {
                    echo "Data berhasil diupdate!";
                } elseif ($_GET['status'] === 'hapus') {
                    echo "Data berhasil dihapus!";
                } else {
                    echo "Status tidak dikenali.";
                }
            } elseif ($pesan) {
                echo htmlspecialchars($pesan);
            }
        ?>
    </div>
<?php endif; ?>

            <form method="POST" action="">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Nama Supplier</label>
                    <input type="text" name="nama" class="form-control" value="<?= $edit_mode ? htmlspecialchars($edit_data['nama']) : '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control"><?= $edit_mode ? htmlspecialchars($edit_data['alamat']) : '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">No Telepon</label>
                    <input type="text" name="telp" class="form-control" value="<?= $edit_mode ? htmlspecialchars($edit_data['no_telp']) : '' ?>">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_premium" id="is_premium" onchange="toggleDiskon()" <?= ($edit_mode && $edit_data['diskon'] > 0) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_premium">Supplier Premium</label>
                </div>

                <div class="mb-3" id="diskon_group" style="display:<?= ($edit_mode && $edit_data['diskon'] > 0) ? 'block' : 'none' ?>;">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" name="diskon" class="form-control" value="<?= $edit_mode ? htmlspecialchars($edit_data['diskon']) : '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Produk yang Dipasok</label>
                    <input type="text" name="produk_fokus" class="form-control" value="<?= $edit_mode ? htmlspecialchars($edit_data['produk_fokus']) : '' ?>">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="<?= $edit_mode ? 'update' : 'simpan' ?>" class="btn btn-success">
                        <?= $edit_mode ? "Simpan Perubahan" : "Simpan" ?>
                    </button>
                    <?php if ($edit_mode): ?>
                        <a href="data_supplier.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Daftar Supplier</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No Telp</th>
                        <th>Produk Fokus</th>
                        <th>Diskon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= htmlspecialchars($row['no_telp']) ?></td>
                            <td><?= htmlspecialchars($row['produk_fokus']) ?></td>
                            <td><?= $row['diskon'] ?>%</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function toggleDiskon() {
    const checkbox = document.getElementById('is_premium');
    const diskonGroup = document.getElementById('diskon_group');
    diskonGroup.style.display = checkbox.checked ? 'block' : 'none';
}
</script>

</body>
</html>