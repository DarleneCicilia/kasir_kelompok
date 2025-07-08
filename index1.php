<?php
// =============================================
// KELAS ENTITAS
// =============================================
class Produk {
    public $nama, $harga, $stok;
    public function __construct($nama, $harga, $stok) {
        $this->nama = $nama;
        $this->harga = $harga;
        $this->stok = $stok;
    }
    public function tampil() {
        return "{$this->nama} | Rp ".number_format($this->harga,0,',','.')." | Stok: {$this->stok}";
    }
}

class Karyawan {
    public $nama, $jabatan;
    public function __construct($nama, $jabatan) {
        $this->nama = $nama;
        $this->jabatan = $jabatan;
    }
    public function tampil() {
        return "{$this->nama} | {$this->jabatan}";
    }
}

class Supplier {
    public $nama, $kontak;
    public function __construct($nama, $kontak) {
        $this->nama = $nama;
        $this->kontak = $kontak;
    }
    public function tampil() {
        return "{$this->nama} | {$this->kontak}";
    }
}

class Transaksi {
    public $kode, $tanggal, $total;
    public function __construct($kode, $tanggal, $total) {
        $this->kode = $kode;
        $this->tanggal = $tanggal;
        $this->total = $total;
    }
    public function tampil() {
        return "{$this->kode} | {$this->tanggal} | Rp ".number_format($this->total,0,',','.');
    }
}

// =============================================
// DATA CONTOH
// =============================================
$produkList = [
    new Produk("Sabun", 5000, 20),
    new Produk("Shampoo", 12000, 15),
];

$karyawanList = [
    new Karyawan("Budi", "Kasir"),
    new Karyawan("Sari", "Admin"),
];

$supplierList = [
    new Supplier("PT Sumber Jaya", "081234567890"),
    new Supplier("CV Maju Makmur", "081398765432"),
];

$penjualanList = [
    new Transaksi("T001", "2025-07-04", 50000),
    new Transaksi("T002", "2025-07-05", 150000),
];

$pembelianList = [
    new Transaksi("PB001", "2025-07-02", 250000),
    new Transaksi("PB002", "2025-07-03", 300000),
];

// =============================================
// HALAMAN
// =============================================
$page = $_GET['page'] ?? 'home';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f0f0f0; }
        header { background:#333; color:#fff; padding:10px; }
        nav a { color:#fff; margin-right:10px; text-decoration:none; }
        .content { padding:20px; }
        footer { background:#333; color:#fff; text-align:center; padding:10px; margin-top:20px; }
        .card { background:#fff; padding:10px; margin:5px 0; border-radius:5px; }
    </style>
</head>
<body>
    <header>
        <h1>APLIKASI KASIR</h1>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="index.php?page=produk">Produk</a>
            <a href="index.php?page=karyawan">Karyawan</a>
            <a href="index.php?page=supplier">Supplier</a>
            <a href="index.php?page=penjualan">Transaksi Penjualan</a>
            <a href="index.php?page=pembelian">Transaksi Pembelian</a>
            <a href="index.php?page=laporan">Laporan</a>
            <a href="index.php?page=stok">Stok</a>
        </nav>
    </header>

    <div class="content">
        <?php
        switch($page){
            case 'produk':
                echo "<h2>Data Produk</h2>";
                foreach($produkList as $p){
                    echo "<div class='card'>{$p->tampil()}</div>";
                }
                break;

            case 'karyawan':
                require('karyawan.php');
                break;

            case 'supplier':
                echo "<h2>Data Supplier</h2>";
                foreach($supplierList as $s){
                    echo "<div class='card'>{$s->tampil()}</div>";
                }
                break;

            case 'penjualan':
                echo "<h2>Transaksi Penjualan</h2>";
                foreach($penjualanList as $t){
                    echo "<div class='card'>{$t->tampil()}</div>";
                }
                break;

            case 'pembelian':
                echo "<h2>Transaksi Pembelian</h2>";
                foreach($pembelianList as $t){
                    echo "<div class='card'>{$t->tampil()}</div>";
                }
                break;

            case 'laporan':
                echo "<h2>Laporan Penjualan</h2>";
                $total = 0;
                foreach($penjualanList as $t){
                    $total += $t->total;
                }
                echo "<div class='card'>Total Pendapatan: Rp ".number_format($total,0,',','.')."</div>";
                break;

            case 'stok':
                echo "<h2>Manajemen Stok</h2>";
                foreach($produkList as $p){
                    echo "<div class='card'>{$p->nama} | Stok: {$p->stok}</div>";
                }
                break;

            default:
                echo "<h2>Selamat datang di Aplikasi Kasir</h2>
                <p>Silakan klik menu di atas untuk melihat data.</p>";
        }
        ?>
    </div>

    <footer>
        &copy; <?= date('Y'); ?> Aplikasi Kasir
    </footer>
</body>
</html>
