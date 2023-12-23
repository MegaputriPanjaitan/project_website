<?php
require 'function.php';

// Cek sudah login atau belum
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cocokkan dengan tabel login
    $cekLogin = pg_query($conn, "SELECT * FROM login WHERE email='$email' AND password='$password'");
    $hitungLogin = pg_num_rows($cekLogin);

    if ($hitungLogin > 0) {

        $_SESSION['log'] = 'True';
        header('location:index.php');
        exit();
    } else {
        // Jika tidak ditemukan di tabel login, cek di tabel user
        $cekUser = pg_query($conn, "SELECT * FROM pengguna WHERE email='$email' AND password='$password'");
        $hitungUser = pg_num_rows($cekUser);

        if ($hitungUser > 0) {
            $user_data = pg_fetch_assoc($cekUser);
            $_SESSION['log'] = 'True';
            $_SESSION['user_id'] = $user_data['user_id'];
            header('location:home.php');
            exit();
        } else {
            // Jika tidak ditemukan di kedua tabel, redirect ke halaman signup.php
            header('location:login.php');
            exit();
        }
    }
}

if(!isset($_SESSION['log'])){

}else{
    header('location:index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                    <div class="card-body">
                                        <form method="POST" action="login.php">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" />
                                                <label for="inputEmail" >Email address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputPassword" name="password"  type="password" placeholder="Password" />
                                                <label for="inputPassword" >Password</label>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button class="btn btn-primary"  name="login" >Login</button>
                                            </div>
                                            
                                            <?php
                                            if (!empty($error_message)) {
                                                echo '<p>' . $error_message . '</p>';
                                            }
                                            ?>
                                            
                                            <p style="text-align: right;">Haven't account? <a href="signup.php">Sign Up</a></p>
                                        </form>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
