<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Cari pengguna di database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        /* Styling untuk halaman login */
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
    background-color: #4caf50; /* Warna hijau untuk tombol login */
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
        <h3>Login</h3>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form action="login.php" method="POST">
            <div class="input-field">
                <input type="text" id="username" name="username" required>
                <label for="username">Username</label>
            </div>
            <div class="input-field">
                <input type="password" id="password" name="password" required>
                <label for="password">Password</label>
            </div>
            <button class="btn waves-effect waves-light" type="submit">Login</button>
        </form>
    </div>
</body>
</html>
