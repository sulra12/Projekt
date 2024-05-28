<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="pl">
<link rel="stylesheet" href="style.css">
<head>
    <title>Logowanie - LubMed</title>
    <?php include 'head.php'; ?>
    <link rel="stylesheet" href="style.css">
    <!-- Dodanie Bootstrap CSS jeśli nie jest już dodany w head.php -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include 'topbar.php'; ?>
    <?php include 'header.php'; ?>

    <main id="main">
        <section class="breadcrumbs">
            <div class="container">

                <div class="d-flex justify-content-between align-items-center">
                    <h2>Resetowanie hasła</h2>
                    <ol>
                        <li><a href="index.php">Strona Główna</a></li>
                        <li><a href="login.php">Logowanie</a></li>
                        <li>Resetowanie hasła</li>
                    </ol>
                </div>

            </div>
        </section>

        <script>
            function validateForm() {
                let username = document.forms["regForm"]["username"].value;
                let email = document.forms["regForm"]["email"].value;
                let password = document.forms["regForm"]["password"].value;

                if (username === "") {
                    alert("Nazwa użytkownika nie może być pusta.");
                    return false;
                }

                if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                    alert("Proszę podać prawidłowy adres email.");
                    return false;
                }

                if (password === "" || password.length < 6) {
                    alert("Hasło nie może być puste i musi zawierać co najmniej 6 znaków.");
                    return false;
                }

                return true;
            }
        </script>

        <section class="inner-page">
            <div class="container">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['token'], $_POST['password'], $_POST['confirm_password'])) {
                    $email = $conn->real_escape_string($_POST['email']);
                    $token = $conn->real_escape_string($_POST['token']);
                    $password = $conn->real_escape_string($_POST['password']);
                    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

                    if ($password !== $confirm_password) {
                        echo "Hasła nie pasują do siebie!";
                        exit;
                    }

                    // Sprawdzenie, czy token i email są prawidłowe
                    $result = $conn->query("SELECT * FROM Patients WHERE email = '$email' AND password_reset_token = '$token'");

                    if ($result->num_rows > 0) {
                        // Ustawienie nowego hasła (pamiętaj o zastosowaniu funkcji hashującej)
                        $conn->query("UPDATE Patients SET password = '$password', password_reset_token = NULL WHERE email = '$email'");
                        echo "<center>Hasło zostało zmienione! Możesz się teraz zalogować.</center>";
                        echo "<center>Za kilka sekund zostaniesz przeniesiony do panelu logowania</center>";
                        sleep(5);
                        header("Location: http://localhost/login.php"); 
                    } else {
                        echo "<center>Nieprawidłowy link resetowania hasła!</center>";
                        echo "<center>Za kilka sekund zostaniesz przeniesiony do panelu logowania</center>";
                        sleep(5);
                        header("Location: http://localhost/login.php"); 
                    }
                } elseif (isset($_GET['email']) && isset($_GET['token'])) {
                    $email = htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8');
                    $token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');
                    echo "<div class='row justify-content-center'>
                            <div class='col-md-6'>
                                <div class='card mt-5'>
                                    <div class='card-body'>
                                        <h3 class='card-title text-center'>Resetowanie hasła</h3>
                                        <form action='forgot_password.php' method='post' onsubmit='return validatePassword()'>
                                            <div class='form-group'>
                                                <label for='password'>Hasło :</label>
                                                <input type='password' class='form-control' id='password' name='password' required>
                                                <label for='confirm_password'>Powtórz hasło :</label>
                                                <input type='password' class='form-control' id='confirm_password' name='confirm_password' required>
                                                <input type='hidden' name='email' value='$email'>
                                                <input type='hidden' name='token' value='$token'>
                                            </div>
                                            <center><button type='submit' class='btn btn-primary'>Zmień hasło</button></center>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                        function validatePassword() {
                            var password = document.getElementById('password').value;
                            var confirmPassword = document.getElementById('confirm_password').value;
                            if (password.length < 7) {
                                alert('Hasło musi zawierać co najmniej 7 znaków.');
                                return false;
                            }
                            if (password !== confirmPassword) {
                                alert('Hasła nie są takie same. Proszę wpisać identyczne hasła.');
                                return false;
                            }
                            return true;
                        }
                        </script>";
                } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
                    $email = $conn->real_escape_string($_POST['email']);

                    // Sprawdzenie, czy użytkownik istnieje w bazie danych
                    $result = $conn->query("SELECT * FROM Patients WHERE email = '$email'");

                    if ($result->num_rows > 0) {
                        $token = bin2hex(random_bytes(50));  // Generowanie bezpiecznego tokena
                        $sql = "UPDATE Patients SET password_reset_token = '$token' WHERE email = '$email'";
                        if ($conn->query($sql) === TRUE) {
                            $endpoint = 'https://api.brevo.com/v3/smtp/email';
                            $api_key = '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';

                            $data = array(
                                'sender' => array(
                                    'name' => 'LubMed',
                                    'email' => 'noreply@lubmed.com'
                                ),
                                'to' => array(
                                    array(
                                        'email' => $email,
                                        'name' => $username
                                    )
                                ),
                                'subject' => 'Resetowanie hasła do konta na LubMed',
                                'htmlContent' => "<html><head></head><body>Kliknij w ten link, aby zresetować swoje hasło: <a href='http://localhost/reset_password.php?email=$email&token=$token'>RESET</a></body></html>"
                            );

                            $options = array(
                                CURLOPT_URL => $endpoint,
                                CURLOPT_POST => true,
                                CURLOPT_POSTFIELDS => json_encode($data),
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_HTTPHEADER => array(
                                    'accept: application/json',
                                    'api-key: ' . $api_key,
                                    'content-type: application/json'
                                )
                            );

                            $curl = curl_init();
                            curl_setopt_array($curl, $options);

                            $response = curl_exec($curl);
                            if ($response === false) {
                                echo '<center>Error: ' . curl_error($curl) . '</center>';
                            } else {
                                echo '<center>Jeśli email jest prawidłowy, zostanie wysłany link do resetowania hasła.</center>';
                            }

                            curl_close($curl);
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    } else {
                        echo "<center>Jeśli email jest prawidłowy, zostanie wysłany link do resetowania hasła.</center>";
                    }
                } else {
                    echo '<div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card mt-5">
                                    <div class="card-body">
                                        <h3 class="card-title text-center">Resetowanie hasła</h3>
                                        <form action="forgot_password.php" method="post">
                                            <div class="form-group">
                                                <label for="email">Adres E-mail:</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                            <center><button type="submit" class="btn btn-primary">Wyślij link
                                                    resetujący</button></center>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
                ?>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

</body>

</html>