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
                    <h2>Rejestracja</h2>
                    <ol>
                        <li><a href="/index.php">Strona główna</a></li>
                        <li><a href="/login.php">Logowanie</a></li>
                        <li>Rejestracja</li>
                    </ol>
                </div>

            </div>
        </section><!-- End Breadcrumbs Section -->

        <script>
            function validateForm() {
                let name = document.forms["regForm"]["name"].value;
                let username = document.forms["regForm"]["username"].value;
                let email = document.forms["regForm"]["email"].value;
                let password = document.forms["regForm"]["password"].value;
                let pesel = document.forms["regForm"]["pesel".value];

                if (name === "") {
                    alert(" Imię nie może być puste.");
                    return false;
                }

                if (username === "") {
                    alert("Nazwisko nie może być puste.");
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

                if (pesel === "" || password.length == 11) {
                    alert("PESEL nie może być pusty i musi zawierać co najmniej 11 znaków.");
                    return false;
                }

                return true;
            }
        </script>

        <section class="inner-page">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
                // Proces rejestracji
                $username = $conn->real_escape_string($_POST['username']);
                $email = $conn->real_escape_string($_POST['email']);
                $password = $conn->real_escape_string($_POST['password']);
                $activation_code = md5(rand());

                $sql = "INSERT INTO Patients (login, email, password) VALUES ('$username','$email','$password')";

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
                        'subject' => 'Aktywacja konta na LubMed',
                        'htmlContent' => "<html><head></head><body>Proszę kliknąć w link, aby aktywować swoje konto: <a href='http://localhost/register.php?code=$activation_code'>Aktywuj teraz!</a></body></html>"
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
                        echo '<center>Proszę sprawdzić swoją skrzynkę mailową, aby aktywować konto.</center>';
                    }

                    curl_close($curl);
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } elseif (isset($_GET['code'])) {

                // Proces aktywacji
                $activation_code = $conn->real_escape_string($_GET['code']);

                $sql = "UPDATE Patients SET isactive = '1' WHERE activation_code = '$activation_code'";

                if ($conn->query($sql) === TRUE) {
                    echo "<center>Konto zostało aktywowane! Możesz się teraz zalogować.</center>";
                } else {
                    echo "<center>Error: " . $sql . "<br>" . $conn->error . "</center>";
                }
                // Proces aktywacji
                $activation_code = $conn->real_escape_string($_GET['code']);

                $sql = "UPDATE users SET isactive = '1' WHERE activation_code = '$activation_code'";

                if ($conn->query($sql) === TRUE) {
                    echo "Konto zostało aktywowane! Możesz się teraz zalogować.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                // Formularz rejestracji
                echo '
                <form name="regForm" onsubmit="return validateForm()" action="" method="POST">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card mt-5">
                        <h3 class="card-title text-center">Rejestracja</h3>
                            <div class="form-group">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Imię:</label>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nazwisko:</label>
                                    <input type="text" class="form-control" name="username" id="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail:</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Hasło:</label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="pesel" class="form-label">PESEL:</label>
                                    <input type="pesel" class="form-control" name="pesel" id="pesel" required>
                                </div>
                                <center><button type="submit" class="btn btn-primary">Zarejestruj się</button></center>
                            </div>
                        </div>
                    </div>
                </div>
                </form>';
            }

            $conn->close();
            ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>

</body>

</html>