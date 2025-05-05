<?php
session_start();
include_once "includes/config.php";
$error = "";
$success = "";

// Ambil email dari session jika diarahkan dari login
$email_value = isset($_SESSION['register_email']) ? $_SESSION['register_email'] : '';
unset($_SESSION['register_email']); // Hapus dari session setelah digunakan

// Proses pendaftaran
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm']);
    
    // Validasi input
    if (empty($nama) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Semua field harus diisi";
    } elseif ($password != $confirmPassword) {
        $error = "Password tidak sama";
    } else {
        // Ganti ke tabel 'ketua'
        $check_email = "SELECT * FROM ketua WHERE email='$email'";
        $result = mysqli_query($conn, $check_email);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Email sudah terdaftar";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert ke tabel ketua
            $query = "INSERT INTO ketua (nama, email, password) VALUES ('$nama', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Pendaftaran berhasil! Silahkan login";
                header("Refresh: 2; URL=login_ketua.php");
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($conn);
            }
        }
    }
}

// Pesan jika diarahkan dari login
$not_found_message = '';
if (isset($_GET['not_found']) && $_GET['not_found'] == 'true') {
    $not_found_message = "Email tidak ditemukan. Silakan daftar terlebih dahulu.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi - SIKOPIN</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <img src="aset/koperasi.jpg" alt="Logo SIKOPIN" class="logo">
      <h2>Buat akun</h2>
      <p>atau <a href="login_ketua.php">masuk ke akun yang sudah ada</a></p>
      <h3>SIKOPIN</h3>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php endif; ?>

      <?php if (!empty($not_found_message)): ?>
        <div class="alert alert-info"><?php echo $not_found_message; ?></div>
      <?php endif; ?>

      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
          <label for="nama">Nama<span class="required">*</span></label>
          <input type="text" id="nama" name="nama" required>
        </div>
        <div class="form-group">
          <label for="email">Alamat email<span class="required">*</span></label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email_value); ?>" required>
        </div>
        <div class="form-group password-toggle">
          <label for="password">Kata sandi<span class="required">*</span></label>
          <input type="password" id="password" name="password" required>
          <span class="toggle-eye" onclick="togglePassword('password')"></span>
        </div>
        <div class="form-group password-toggle">
          <label for="confirm">Konfirmasi kata sandi<span class="required">*</span></label>
          <input type="password" id="confirm" name="confirm" required>
          <span class="toggle-eye" onclick="togglePassword('confirm')"></span>
        </div>
        <button type="submit" class="btn-login">Buat akun</button>
      </form>
    </div>
  </div>

  <script>
    function togglePassword(id) {
      const field = document.getElementById(id);
      if (field.type === "password") {
        field.type = "text";
      } else {
        field.type = "password";
      }
    }
  </script>
</body>
</html>