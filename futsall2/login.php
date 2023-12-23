<!DOCTYPE html>
<html>

<head>
    <title>Login Futsal Kuskus Bakar</title>
    <link rel="stylesheet" type="text/css" href="log.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="p">
        <form action="cek_login.php" method="POST">
            <h1>LOGIN</h1>
            <div class="input-box">
                <i class='bx bxs-user'></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <i class='bx bxs-lock'></i>
                <input type="password" name="password" placeholder="Password" id="password" required>
            </div>
            <div class="input-box">
                <i class='bx bx-list-ul'></i>
                <select name="kategori" class="select-box" required>
                    <option value="" disabled selected>Kategori</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <div>
                <input type="checkbox" id="showPassword" onclick="togglePassword()">
                <label for="showPassword">Show Password</label>
            </div><br>
            <button type="submit" class="btn" name="login" value="login">Login</button>
            <button type="button" class="btnn" onclick="window.location.href='register.php'">Register</button>
        </form>
    </div>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
        }
    </script>
</body>

</html>