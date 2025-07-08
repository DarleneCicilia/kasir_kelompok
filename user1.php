<?php
class User {
    protected $id;
    protected $username;
    protected $email;

    public function __construct($id = null, $username = null, $email = null) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }
}

class UserManager extends User {
    private $conn;

    public function __construct($conn) {
        parent::__construct(); // Untuk memastikan konstruktor induk terpanggil
        $this->conn = $conn;
    }

    public function registerUser($username, $password, $email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['status' => false, 'message' => 'Username sudah terdaftar.'];
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['status' => false, 'message' => 'Email sudah terdaftar.'];
        }
        $stmt->close();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => true, 'message' => 'Pendaftaran berhasil!'];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['status' => false, 'message' => 'Pendaftaran gagal: ' . $error];
        }
    }

    public function loginUser($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, password, email FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            if (password_verify($password, $user_data['password'])) {
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['username'] = $user_data['username'];
                $_SESSION['email'] = $user_data['email'];

                $this->id = $user_data['id'];
                $this->username = $user_data['username'];
                $this->email = $user_data['email'];
                return true;
            }
        }
        return false;
    }

    public function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $stmt = $this->conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                return new self($this->conn); // return sebagai UserManager juga bisa
            }
        }
        return null;
    }

    public function logoutUser() {
        session_unset();
        session_destroy();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login & Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: white;
            color: #333;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 350px;
        }
        h2 {
            margin-bottom: 1rem;
            text-align: center;
        }
        input {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
        }
        button {
            width: 100%;
            padding: 0.8rem;
            background: #667eea;
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #5a67d8;
        }
        .toggle {
            text-align: center;
            margin-top: 1rem;
        }
        .toggle a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required/>
        <input type="password" name="password" placeholder="Password" required/>
        <button type="submit">Login</button>
    </form>
    <div class="toggle">Belum punya akun? <a href="register.html">Daftar</a></div>
</div>

</body>
</html>