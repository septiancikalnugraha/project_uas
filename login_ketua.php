<?php
session_start();
include_once "includes/config.php";
$error = "";

// Cek jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location:dashboard.php");
    exit();
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi";
    } else {
        // Ganti nama tabel dari 'users' menjadi 'ketua'
        $query = "SELECT * FROM ketua WHERE email='$email'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect ke dashboard
                header("Location:dashboard.php");
                exit();
            } else {
                $error = "Password salah";
            }
        } else {
            // Email tidak ditemukan, arahkan ke halaman registrasi
            $_SESSION['register_email'] = $email;
            header("Location: registrasi_ketua.php?not_found=true");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Ketua - SIKOPIN</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <img src="aset/koperasi.jpg" alt="Logo SIKOPIN" class="logo">
      <h2>Masuk Sebagai Ketua</h2>
      <h3>SIKOPIN</h3>
      
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
          <label for="email">Alamat email<span class="required">*</span></label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Kata sandi<span class="required">*</span></label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" required>
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
          </div>
        </div>
        <div class="form-remember">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Ingat saya</label>
        </div>
        <button type="submit" class="btn-login">Masuk</button>
      </form>
      
    </div>
  </div>
  <script>
    function togglePassword() {
      const password = document.getElementById("password");
      password.type = password.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>