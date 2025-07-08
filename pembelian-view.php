<?php
include_once 'pembelian.php';

$conn = new mysqli("localhost", "root", "", "kasir_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil semua data untuk ditampilkan
$data = $conn->query("SELECT * 
    FROM pembelian ");
//     echo '<pre>';
// print_r($data->fetch_all(MYSQLI_ASSOC)); // tampilkan semua hasil sebagai array
// echo '</pre>';
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
            <li class="nav-item"> <a href="pembelian-view.php" class="nav-link active"> <i
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

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Daftar Supplier</h5>
        </div>
        <div class="card-body">
            <div class="m-2">
                <a href="data_pembelian.php" class="btn btn-primary">Tambah Pembelian</a>
            </div>
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembelian</th>
                        <th>Tanggal Pengiriman</th>
                        <th>Kepada</th>
                        <th>Alamat</th>
                        <th>Termin</th>
                        <th>Pajak</th>
                        <th>Total Harga</th>
                        <th>Uang Dibayar</th>
                        <th>Kembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pembelian']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pengiriman']) ?></td>
                            <td><?= htmlspecialchars($row['kepada']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= htmlspecialchars($row['termin']) ?></td>
                            <td><?= htmlspecialchars($row['total_pajak']) ?></td>
                            <td><?= htmlspecialchars($row['total_harga']) ?></td>
                            <td><?= htmlspecialchars($row['uang_dibayar']) ?></td>
                            <td><?= htmlspecialchars($row['kembalian']) ?></td>
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
</body><!--end::Body-->


</html>
