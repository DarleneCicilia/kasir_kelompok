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
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>KASIR</title><!--begin::Accessibility Meta Tags-->
  <meta name="title" content="KASIR">
  <link rel="preload" href="css/adminlte.css" as="style"><!--end::Accessibility Features--><!--begin::Fonts-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    crossorigin="anonymous"><!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
  <link rel="stylesheet" href="css/adminlte.css"><!--end::Required Plugin(AdminLTE)--><!-- apexcharts -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
    integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
</head> 

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary"> <!--begin::App Wrapper-->
  <div class="app-wrapper"> <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
      <div class="container-fluid"> <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
          <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                class="bi bi-list"></i> </a> </li>
        </ul> 
      </div> 
    </nav> <!--end::Header--> <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark"> <!--begin::Sidebar Brand-->
      <div class="sidebar-brand"><span
            class="brand-text fw-light">KASIR</span> </div>
      <!--end::Sidebar Brand--> <!--begin::Sidebar Wrapper-->
      <div class="sidebar-wrapper">
        <nav class="mt-2"> <!--begin::Sidebar Menu-->
          <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
            aria-label="Main navigation" data-accordion="false" id="navigation">
            <li class="nav-item"> <a href="index.php" class="nav-link "> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item"> <a href="data_produk.php" class="nav-link"> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Produk</p>
              </a>
            </li>
            <li class="nav-item"> <a href="data_karyawan.php" class="nav-link"> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Karyawan</p>
              </a>
            </li>
            <li class="nav-item"> <a href="data_supplier.php" class="nav-link active"> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Supplier</p>
              </a>
            </li>
            <li class="nav-item"> <a href="kasir.php" class="nav-link "> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Transaksi Penjualan</p>
              </a>
            </li>
            <li class="nav-item"> <a href="pembelian-view.php" class="nav-link "> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Transaksi Pembelian</p>
              </a>
            </li>
            <li class="nav-item"> <a href="" class="nav-link "> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Laporan</p>
              </a>
            </li>
            <li class="nav-item"> <a href="data_manajemenstok.php" class="nav-link"> <i
                  class="nav-icon bi bi-palette"></i>
                <p>Stok</p>
              </a>
            </li>
          </ul> <!--end::Sidebar Menu-->
        </nav>
      </div> <!--end::Sidebar Wrapper-->
    </aside> <!--end::Sidebar--> <!--begin::App Main-->
    <main class="app-main"> <!--begin::App Content Header-->
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
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
  <script src="../js/adminlte.js"></script>
<script>
function toggleDiskon() {
    const checkbox = document.getElementById('is_premium');
    const diskonGroup = document.getElementById('diskon_group');
    diskonGroup.style.display = checkbox.checked ? 'block' : 'none';
}
</script>
</body><!--end::Body-->


</html>

<!-- <script>
function toggleDiskon() {
    const checkbox = document.getElementById('is_premium');
    const diskonGroup = document.getElementById('diskon_group');
    diskonGroup.style.display = checkbox.checked ? 'block' : 'none';
}
</script>

</body>
</html> -->