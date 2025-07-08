<?php

// Class Produk
class Product {
    public $id;
    public $name;
    public $price;
    public $stock;

    public function __construct($id, $name, $price, $stock) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getInfoArray() {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }
}

// Class Toko Produk
class ProductStore {
    private $products = [];

    public function addProduct(Product $product) {
        $this->products[] = $product;
    }

    public function getAllProducts() {
        return $this->products;
    }
}

// Buat toko dan data dummy
$store = new ProductStore();
$store->addProduct(new Product(1, "Kemeja Polos", 95000, 10));
$store->addProduct(new Product(2, "Celana Jeans", 150000, 5));
$store->addProduct(new Product(3, "Sepatu Sneaker", 250000, 3));

$allProducts = $store->getAllProducts();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Produk Toko</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px #ccc;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .price {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>🛍️ Daftar Produk Toko</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allProducts as $product): 
                $info = $product->getInfoArray();
            ?>
            <tr>
                <td><?= $info['id'] ?></td>
                <td><?= htmlspecialchars($info['name']) ?></td>
                <td class="price">Rp<?= number_format($info['price'], 0, ',', '.') ?></td>
                <td><?= $info['stock'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
