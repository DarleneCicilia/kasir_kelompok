<?php
include 'db_config.php';

$success = "";
$error = "";

// Proses simpan produk
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama  = trim($_POST['nama_stok']);
    $satuan = trim($_POST['satuan']);
    $stok  = intval($_POST['stok']);

    if ($nama && $satuan && $stok > 0) {
        $stmt = $conn->prepare("INSERT INTO produk_stok (nama_stok, satuan, stok) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nama, $satuan, $stok);

        if ($stmt->execute()) {
            $success = "Produk berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan produk.";
        }
        $stmt->close();
    } else {
        $error = "Semua field wajib diisi dengan benar.";
    }
}

// Ambil semua produk dari database
$result = $conn->query("SELECT * FROM produk_stok ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .box {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h5 i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h3 class="text-center mb-4"><i class="bi bi-box-seam"></i> Manajemen Produk</h3>

    <div class="row">
        <!-- Kiri: Form Tambah Produk -->
        <div class="col-md-6 mb-4">
            <div class="box">
                <h5><i class="bi bi-plus-circle text-primary"></i> Tambah Produk Baru</h5>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-2">
                        <label>Nama Stok:</label>
                        <input type="text" name="nama_stok" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Satuan:</label>
                        <input type="text" name="satuan" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Stok:</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Simpan Produk
                    </button>
                </form>
            </div>
        </div>

        <!-- Kanan: Tabel Produk -->
        <div class="col-md-6">
            <div class="box">
                <h5><i class="bi bi-table"></i> Daftar Produk</h5>

                <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Stok</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_stok']) ?></td>
                                <td><?= htmlspecialchars($row['satuan']) ?></td>
                                <td><?= $row['stok'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada produk.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
