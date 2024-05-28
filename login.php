<?php
include 'connect.php';

$message = '';

// Sprawdź, czy formularz został przesłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $password = $_POST['password'];

    // Sprawdź, czy istnieje użytkownik o podanym imieniu i nazwisku
    $sql = "SELECT * FROM patients WHERE first_name = ? AND last_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $surname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Użytkownik istnieje, sprawdź hasło
        $user = $result->fetch_assoc();
        if ($user['password'] === $password) {
            // Dane logowania są poprawne, przekieruj użytkownika na question.php
            header("Location: question.php");
            exit();
        } else {
            // Hasło jest niepoprawne
            $message = "Błędne hasło.";
        }
    } else {
        // Użytkownik nie istnieje
        $message = "Nie ma takiego konta - proszę załóż konto.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie - LubMed</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'topbar.php'; ?>
    <?php include 'header.php'; ?>

    <section class="inner-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h3 class="card-title text-center">Logowanie</h3>
                            <?php if ($message): ?>
                                <div class="alert alert-danger"><?php echo $message; ?></div>
                            <?php endif; ?>
                            <form action="login.php" method="post">
                                <div class="form-group">
                                    <label for="name">Imię:</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="surname">Nazwisko:</label>
                                    <input type="text" class="form-control" id="surname" name="surname" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Hasło:</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <center><button type="submit" class="btn btn-primary">Zaloguj się</button></center>
                            </form>
                            <div class="mt-3">
                                <a href="register.php">Nie masz konta? Zarejestruj się!</a>
                            </div>
                            <div class="mt-2">
                                <a href="forgot_password.php">Zapomniałeś hasła?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
