<?php
class Pembelian {
    protected $conn;
    protected $data;

    public function __construct(mysqli $db) {
        $this->conn = $db;
        $this->data = [];
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function simpanTransaksi() {
        $stmt = $this->conn->prepare(
            "INSERT INTO pembelian 
            (tanggal_pembelian, tanggal_pengiriman, kepada, alamat, termin, tanggal_jatuh_tempo, total_pajak, total_harga, uang_dibayar, kembalian) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssssssddd",
            $this->data['tanggal_pembelian'],
            $this->data['tanggal_pengiriman'],
            $this->data['kepada'],
            $this->data['alamat'],
            $this->data['termin'],
            $this->data['tanggal_jatuh_tempo'],
            $this->data['total_pajak'],
            $this->data['total_harga'],
            $this->data['uang_dibayar'],
            $this->data['kembalian']
        );

        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function hapusTransaksi($id) {
        $this->conn->query("DELETE FROM detail_pembelian WHERE id_pembelian = $id");
        return $this->conn->query("DELETE FROM pembelian WHERE id_pembelian = $id");
    }
}

class DetailPembelian extends Pembelian {

    public function simpanDetail($id_pembelian, $items) {
        foreach ($items as $item) {
            $stmt = $this->conn->prepare(
                "INSERT INTO detail_pembelian 
                (id_pembelian, id_produk, nama_produk, quantity, harga_satuan, subtotal, pajak) 
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param("issiidd", 
                $id_pembelian,
                $item['id_produk'],
                $item['nama_produk'],
                $item['jumlah'],
                $item['harga'],
                $item['subtotal'],
                $item['pajak']
            );

            $stmt->execute();
        }
    }

    public function tampilkanDetail($id_pembelian) {
        $result = $this->conn->query("SELECT * FROM detail_pembelian WHERE id_pembelian = $id_pembelian");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>