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
            <li class="nav-item"> <a href="data_supplier.php" class="nav-link "> <i
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
            <li class="nav-item"> <a href="data_manajemenstok.php" class="nav-link active"> <i
                  class="nav-icon bi bi-palette active"></i>
                <p>Stok</p>
              </a>
            </li>
          </ul> <!--end::Sidebar Menu-->
        </nav>
      </div> <!--end::Sidebar Wrapper-->
    </aside> <!--end::Sidebar--> <!--begin::App Main-->
    <main class="app-main"> <!--begin::App Content Header-->
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
    </main>
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