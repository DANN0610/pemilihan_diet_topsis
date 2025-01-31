<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password

    // Cek apakah username sudah ada di database
    $sql_check = "SELECT * FROM users WHERE username = '$username'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Username sudah ada
        $error = "Username sudah digunakan, silakan pilih yang lain.";
    } else {
        // Simpan ke database
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "Pendaftaran berhasil! <a href='login.php'>Login</a>";
        } else {
            echo "Gagal mendaftar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran</title>
    <style>
        /* Styling untuk halaman pendaftaran */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9; /* Warna latar belakang yang lembut */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #ffffff; /* Warna putih untuk latar belakang form */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px; /* Agar sudut form menjadi melengkung */
        }

        h3 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .input-field {
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #4caf50; /* Warna hijau saat fokus */
            outline: none;
        }

        label {
            font-size: 14px;
            color: #555;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4caf50; /* Warna hijau untuk tombol daftar */
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #388e3c; /* Warna hijau lebih gelap saat hover */
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #4caf50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            text-align: center;
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Daftar Akun</h3>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form action="register.php" method="POST">
            <div class="input-field">
                <input type="text" id="username" name="username" required>
                <label for="username">Username</label>
            </div>
            <div class="input-field">
                <input type="password" id="password" name="password" required>
                <label for="password">Password</label>
            </div>
            <button type="submit">Daftar</button>
            <a href="login.php">Sudah punya akun? Login</a>
        </form>
    </div>
</body>
</html>
