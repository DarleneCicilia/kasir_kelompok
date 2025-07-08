<?php
session_start();
include 'db_config.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit();
}

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $koneksi->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "<p style='color:green'>Registrasi berhasil. Silakan login.</p>";
    } else {
        echo "<p style='color:red'>Registrasi gagal: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: auth.php");
            exit();
        } else {
            echo "<p style='color:red'>Password salah.</p>";
        }
    } else {
        echo "<p style='color:red'>Username tidak ditemukan.</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login & Register - Kasir App</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #2193b0, #6dd5ed);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 60px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="password"] {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .toggle-link {
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .success { color: green; }
        .error { color: red; }

        .logout {
            text-align: center;
            margin-top: 20px;
        }

        .logout a {
            color: red;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
<?php if (isset($_SESSION['user_id'])): ?>
    <h2>👋 Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
    <div class="logout">
        <a href="?logout=1">Logout</a><br><br>
        <a href="index.php">Masuk ke Dashboard</a>
    </div>
<?php else: ?>
    <h2><?= isset($_POST['register']) ? 'Daftar Akun' : 'Login Kasir' ?></h2>

    <?php
    // Tampilkan pesan jika ada
    if (isset($message)) {
        echo "<div class='message " . ($message['type'] ?? 'error') . "'>" . htmlspecialchars($message['text']) . "</div>";
    }
    ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required value="<?= $_POST['username'] ?? '' ?>">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="<?= isset($_POST['register']) ? 'register' : 'login' ?>">
            <?= isset($_POST['register']) ? 'Daftar' : 'Login' ?>
        </button>
    </form>

    <div class="toggle-link" onclick="toggleForm()">
        <?= isset($_POST['register']) ? 'Sudah punya akun? Login' : 'Belum punya akun? Daftar' ?>
    </div>

    <form id="toggleForm" method="post" style="display:none;">
        <input type="hidden" name="<?= isset($_POST['register']) ? 'login' : 'register' ?>" value="1">
    </form>

    <script>
        function toggleForm() {
            document.getElementById('toggleForm').submit();
        }
    </script>
<?php endif; ?>
</div>

</body>
</html>
