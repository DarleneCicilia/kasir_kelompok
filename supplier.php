<?php
// Base class
class Supplier {
    protected $id;
    protected $nama;
    protected $alamat;
    protected $no_telp;

    public function __construct($nama, $alamat, $no_telp) {
        $this->nama = $nama;
        $this->alamat = $alamat;
        $this->no_telp = $no_telp;
    }

    // Encapsulation
    public function getNama() {
        return $this->nama;
    }

    public function setNama($nama) {
        $this->nama = $nama;
    }

    public function getInfo() {
        return "{$this->nama}, {$this->alamat}, Telp: {$this->no_telp}";
    }
}

// Inheritance
class SupplierPremium extends Supplier {
    private $diskon;

    public function __construct($nama, $alamat, $no_telp, $diskon) {
        parent::__construct($nama, $alamat, $no_telp);
        $this->diskon = $diskon;
    }

    public function hitungHargaSetelahDiskon($hargaAwal) {
        return $hargaAwal - ($hargaAwal * $this->diskon / 100);
    }
    // Polymorphism
    public function getInfo() {
        return parent::getInfo() . " (Diskon: {$this->diskon}%)";
    }
}

