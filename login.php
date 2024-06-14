<?php
session_start();
include("connect.php");

$loginErrors = [];
$loginSuccessMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST["phone"];
    $password = $_POST["password"];

    // Basic validation
    if (empty($phone)) {
        $loginErrors['phone'] = "Phone is required.";
    }
    if (empty($password)) {
        $loginErrors['password'] = "Password is required.";
    }

    if (empty($loginErrors)) {
        // Sanitize inputs before using in query
        $phone = $conn->real_escape_string($phone);

        // Prepare the SQL statement
        $sql = "SELECT * FROM customer WHERE Phone = '$phone'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $user['Password'])) {
                $_SESSION['user'] = $user;
                header("Location: home.php");
                exit();
            } else {
                $loginErrors['password'] = "Incorrect password.";
            }
        } else {
            $loginErrors['phone'] = "No user found with this phone number.";
        }
    }
}
$conn->close();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 10;
            background: rgba(0, 0, 0, 0.9);
        }

        .card {
            border: none;
        }

        .nav-link {
            color: white !important;
            font-size: larger;
            font-weight: 500;
            font-family: Arial, Helvetica, sans-serif;
        }

        .error-message {
            color: red;
            font-size: 0.875em;
        }

        .success-message {
            color: green;
            font-size: 1em;
            margin-bottom: 1em;
        }

        .title {
            text-align: center;
            font-size: 2em;
            font-weight: bold;
            margin-top: 1em;
            margin-bottom: 1em;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img style="width: 20%;height: 20%; margin-left: 35%;" src="https://www.bhdstar.vn/wp-content/uploads/2023/08/logo.png" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Showtimes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">Ticket purchase history</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Member</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto me-5">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container" style="margin-top: 5%; width: 50%;">
        <div class="title" style="color: blue;">Log in to your account</div>
        <?php if (!empty($loginSuccessMessage)) : ?>
            <p class="success-message"><?php echo $loginSuccessMessage; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                <?php if (isset($loginErrors['phone'])) : ?>
                    <p class="error-message"><?php echo $loginErrors['phone']; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control">
                <?php if (isset($loginErrors['password'])) : ?>
                    <p class="error-message"><?php echo $loginErrors['password']; ?></p>
                <?php endif; ?>
            </div>
            <button style="width: 100%; margin-top: 1rem;" type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>

</html>