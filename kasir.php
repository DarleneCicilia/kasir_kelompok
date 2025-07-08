<?php
session_start();
include 'db_config.php';

class PhysicalProduct {
    private string $name;
    private float $price;
    private string $weight;
    private int $quantity = 1;

    public function __construct(string $name, float $price, string $weight, int $quantity = 1) {
        $this->name = $name;
        $this->price = $price;
        $this->weight = $weight;
        $this->quantity = $quantity;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity) {
        $this->quantity = $quantity;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getWeight(): string {
        return $this->weight;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function setPrice(float $price) {
        $this->price = $price;
    }

    public function setWeight(string $weight) {
        $this->weight = $weight;
    }
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$editProduct = null;
$editIndex = null;

if (isset($_GET['edit'])) {
    $editIndex = intval($_GET['edit']);
    if (isset($_SESSION['cart'][$editIndex])) {
        $editProduct = unserialize($_SESSION['cart'][$editIndex]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $weightInput = trim($_POST['weight'] ?? '');
    $weight = ($weightInput === '') ? '-' : $weightInput;
    $quantity = intval($_POST['quantity'] ?? 1);
    $editIndex = $_POST['edit_index'] ?? null;

    if ($editIndex !== null && isset($_SESSION['cart'][$editIndex])) {
        $product = unserialize($_SESSION['cart'][$editIndex]);
        $product->setName($name);
        $product->setPrice($price);
        $product->setWeight($weight);
        $product->setQuantity($quantity);
        $_SESSION['cart'][$editIndex] = serialize($product);
    } else {
        $product = new PhysicalProduct($name, $price, $weight, $quantity);
        $_SESSION['cart'][] = serialize($product);
    }
    header('Location: kasir.php');
    exit;
}

if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
    header('Location: kasir.php');
    exit;
}

if (isset($_GET['reset'])) {
    unset($_SESSION['cart']);
    header('Location: kasir.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
$totalWeight = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Barang Toko</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 100%;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            text-align: center;
        }
        .main-content {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .form-card, .cart-card {
            flex: 1;
            min-width: 420px;
            background-color: #fafafa;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn-submit, .btn-reset, .btn-action {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-reset {
            background-color: #6c757d;
        }
        .btn-action.delete {
            background-color: #dc3545;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cart-table th, .cart-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        .total-section {
            margin-top: 20px;
            font-weight: bold;
        }
        @media (max-width: 900px) {
            .main-content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🛒 Transaksi Penjualan</h1>

    <div class="main-content">
        <div class="form-card">
            <h2><?= $editProduct ? '✏️ Edit Produk' : '➕ Tambah Produk' ?></h2>
            <form method="POST" action="">
                <input type="hidden" name="edit_index" value="<?= $editIndex !== null ? htmlspecialchars($editIndex) : '' ?>">

                <div class="form-group">
                    <label for="name">Nama Produk:</label>
                    <input type="text" id="name" name="name" required value="<?= $editProduct ? htmlspecialchars($editProduct->getName()) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="price">Harga (Rp):</label>
                    <input type="number" id="price" name="price" step="0.01" required value="<?= $editProduct ? htmlspecialchars($editProduct->getPrice()) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="weight">Berat (kg):</label>
                    <input type="text" id="weight" name="weight" value="<?= $editProduct ? htmlspecialchars($editProduct->getWeight()) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="quantity">Jumlah:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required value="<?= $editProduct ? htmlspecialchars($editProduct->getQuantity()) : 1 ?>">
                </div>

                <button type="submit" class="btn-submit"><?= $editProduct ? 'Update' : 'Tambah' ?> Produk</button>
            </form>
        </div>

        <div class="cart-card">
            <h2>🧾 Daftar Belanja</h2>
            <p><strong>Total Produk:</strong> <?= count($cart) ?> item</p>

            <?php if (empty($cart)): ?>
                <p class="empty">Keranjang masih kosong.</p>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Total Berat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart as $i => $item): 
                        $product = unserialize($item);
                        $subtotal = $product->getPrice() * $product->getQuantity();
                        $weightVal = is_numeric($product->getWeight()) ? floatval($product->getWeight()) : 0;
                        $subweight = $weightVal * $product->getQuantity();
                        $total += $subtotal;
                        $totalWeight += $subweight;
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($product->getName()) ?></td>
                            <td>Rp<?= number_format($product->getPrice(), 0, ',', '.') ?></td>
                            <td><?= $product->getQuantity() ?></td>
                            <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                            <td><?= is_numeric($product->getWeight()) ? number_format($subweight, 2) . ' kg' : '-' ?></td>
                            <td>
                                <a href="?edit=<?= $i ?>" class="btn-action">Edit</a>
                                <a href="?delete=<?= $i ?>" onclick="return confirm('Yakin hapus produk ini?')" class="btn-action delete">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="total-section">
                    <p>Total Harga: Rp<?= number_format($total, 0, ',', '.') ?></p>
                    <p>Total Berat: <?= number_format($totalWeight, 2) ?> kg</p>
                    <a href="?reset=true" class="btn-reset">Reset Keranjang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
