<?php
abstract class Produk {
    protected $id, $nama, $satuan, $stok;

    public function __construct($id, $nama, $satuan, $stok) {
        $this->id = $id;
        $this->nama = $nama;
        $this->satuan = $satuan;
        $this->stok = $stok;
    }

    abstract public function tampilkanInfo();

    public function getNama() {
        return $this->nama;
    }

    public function getStok() {
        return $this->stok;
    }

    public function setStok($jumlah) {
        if ($jumlah < 0) throw new Exception("Stok tidak boleh negatif");
        $this->stok = $jumlah;
    }
}

class ProdukStok extends Produk {
    private $id_pembelian;

    public function __construct($id, $id_pembelian, $nama, $satuan, $stok) {
        parent::__construct($id, $nama, $satuan, $stok);
        $this->id_pembelian = $id_pembelian;
    }

    public function tampilkanInfo() {
        return "[$this->id] $this->nama - Rp $this->satuan ($this->stok pcs)";
    }

    public function getPembelianId() {
        return $this->id_pembelian;
    }

    public function getId() {
    return $this->id;
    }
}