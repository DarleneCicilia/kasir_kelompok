<?php
session_start();

class PhysicalProduct {
    private string $name;
    private float $price;
    private float $weight;
    private int $quantity;

    public function __construct(string $name, float $price, float $weight, int $quantity = 1) {
        $this->name = $name;
        $this->price = $price;
        $this->weight = $weight;
        $this->quantity = $quantity;
    }

    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getWeight(): float { return $this->weight; }
    public function getQuantity(): int { return $this->quantity; }

    public function setName(string $name) { $this->name = $name; }
    public function setPrice(float $price) { $this->price = $price; }
    public function setWeight(float $weight) { $this->weight = $weight; }
    public function setQuantity(int $quantity) { $this->quantity = $quantity; }
}

if (!isset($_GET['edit'])) {
    header('Location: kasir.php');
    exit;
}

$editIndex = intval($_GET['edit']);

if (!isset($_SESSION['cart'][$editIndex])) {
    header('Location: kasir.php');
    exit;
}

$product = unserialize($_SESSION['cart'][$editIndex]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $weight = floatval($_POST['weight'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);

    $product->setName($name);
    $product->setPrice($price);
    $product->setWeight($weight);
    $product->setQuantity($quantity);

    $_SESSION['cart'][$editIndex] = serialize($product);

    header('Location: kasir.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .btn, .btn-cancel {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn {
            background-color: #007bff;
            color: white;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-cancel {
            background-color: #ccc;
            color: black;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Produk</h1>

    <form method="post">
        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($product->getName()) ?>">
        </div>

        <div class="form-group">
            <label for="price">Harga (Rp)</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= $product->getPrice() ?>">
        </div>

        <div class="form-group">
            <label for="weight">Berat (kg)</label>
            <input type="number" id="weight" name="weight" step="0.01" min="0" required value="<?= $product->getWeight() ?>">
        </div>

        <div class="form-group">
            <label for="quantity">Jumlah</label>
            <input type="number" id="quantity" name="quantity" min="1" required value="<?= $product->getQuantity() ?>">
        </div>

        <button type="submit" class="btn">Update Produk</button>
        <a href="kasir.php" class="btn-cancel">Batal</a>
    </form>
</div>
</body>
</html>