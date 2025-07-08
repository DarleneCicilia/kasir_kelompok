<?php
$koneksi = new mysqli("localhost", "root", "", "kasir");

// proses tambah
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $gaji = $_POST['gaji'];
    $koneksi->query("INSERT INTO karyawan (nama, jabatan, gaji) VALUES ('$nama', '$jabatan', '$gaji')");
    header("Location: karyawan.php");
    exit;
}

// proses edit
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $gaji = $_POST['gaji'];
    $koneksi->query("UPDATE karyawan SET nama='$nama', jabatan='$jabatan', gaji='$gaji' WHERE id=$id");
    header("Location: karyawan.php");
    exit;
}

// proses hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM karyawan WHERE id=$id");
    header("Location: karyawan.php");
    exit;
}

// data edit
$dataEdit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $dataEdit = $koneksi->query("SELECT * FROM karyawan WHERE id=$id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
        table {
            margin: auto;
            border-collapse: collapse;
            width: 80%;
        }
        th {
            background-color: #333;
            color: #fff;
            padding: 10px;
        }
        td {
            background-color: #fff;
            padding: 10px;
            text-align: left;
        }
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        form {
            margin: auto;
            width: 50%;
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ccc;
        }
        label, input {
            display: block;
            margin-bottom: 8px;
        }
        button {
            margin-top: 10px;
        }
        .aksi a {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h2>Data Karyawan</h2>

    <?php if ($dataEdit) { ?>
        <form method="post">
            <h3>Edit Karyawan</h3>
            <input type="hidden" name="id" value="<?= $dataEdit['id'] ?>">
            <label>Nama</label>
            <input type="text" name="nama" value="<?= $dataEdit['nama'] ?>" required>
            <label>Jabatan</label>
            <input type="text" name="jabatan" value="<?= $dataEdit['jabatan'] ?>" required>
            <label>Gaji</label>
            <input type="number" name="gaji" value="<?= $dataEdit['gaji'] ?>" required>
            <button type="submit" name="edit">Simpan Perubahan</button>
            <a href="karyawan.php">Batal</a>
        </form>
    <?php } else { ?>
        <form method="post">
            <h3>Tambah Karyawan</h3>
            <label>Nama</label>
            <input type="text" name="nama" required>
            <label>Jabatan</label>
            <input type="text" name="jabatan" required>
            <label>Gaji</label>
            <input type="number" name="gaji" required>
            <button type="submit" name="tambah">Simpan</button>
        </form>
    <?php } ?>

    <br>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Gaji</th>
            <th>Aksi</th>
        </tr>
        <?php
        $query = $koneksi->query("SELECT * FROM karyawan");
        while ($data = $query->fetch_assoc()) {
            $gaji = number_format($data['gaji'], 0, ',', '.');
            echo "<tr>
                <td>{$data['id']}</td>
                <td>{$data['nama']}</td>
                <td>{$data['jabatan']}</td>
                <td>Rp {$gaji}</td>
                <td class='aksi'>
                    <a href='karyawan.php?edit={$data['id']}'>Edit</a>
                    <a href='karyawan.php?hapus={$data['id']}' onclick=\"return confirm('Hapus data ini?')\">Hapus</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</body>
</html>
