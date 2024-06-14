<?php
include("connect.php");

$errors = [];
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $dateOfBirth = $_POST["dateOfBirth"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $address = $_POST["address"];

    // Basic validation
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    if (empty($phone)) {
        $errors['phone'] = "Phone is required.";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }
    if (empty($dateOfBirth)) {
        $errors['dateOfBirth'] = "Date of Birth is required.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }
    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = "Confirm Password is required.";
    }
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match.";
    }
    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }

    if (empty($errors)) {
        // Sanitize inputs before using in query
        $name = $conn->real_escape_string($name);
        $phone = $conn->real_escape_string($phone);
        $email = $conn->real_escape_string($email);
        $dateOfBirth = $conn->real_escape_string($dateOfBirth);
        $address = $conn->real_escape_string($address);

        // Check for duplicate email
        $emailCheckSql = "SELECT * FROM customer WHERE Email = '$email'";
        $emailCheckResult = $conn->query($emailCheckSql);
        if ($emailCheckResult->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }

        // Check for duplicate phone
        $phoneCheckSql = "SELECT * FROM customer WHERE Phone = '$phone'";
        $phoneCheckResult = $conn->query($phoneCheckSql);
        if ($phoneCheckResult->num_rows > 0) {
            $errors['phone'] = "Phone number already exists.";
        }

        if (empty($errors)) {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL statement
            $sql = "INSERT INTO customer (Name, Email, Phone, Address, DateOfBirth, Password) 
                    VALUES ('$name', '$email', '$phone', '$address', '$dateOfBirth', '$hashedPassword')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                $successMessage = "Registration successful!";
            } else {
                $errors['database'] = "Error: " . $sql . "<br>" . $conn->error;
            }
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
    <title>Registration</title>
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
                        <a class="nav-link" href="#">Memmber</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto me-5">
                    <li class="nav-item">
                        <a class="nav-link" href="/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container" style="margin-top: 5%; width: 50%;">
        <?php if (!empty($successMessage)) : ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="mb-3 row form-group">
                <div class="col">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                    <?php if (isset($errors['name'])) : ?>
                        <p class="error-message"><?php echo $errors['name']; ?></p>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                    <?php if (isset($errors['phone'])) : ?>
                        <p class="error-message"><?php echo $errors['phone']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                <?php if (isset($errors['email'])) : ?>
                    <p class="error-message"><?php echo $errors['email']; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Date Of Birth</label>
                <input type="date" name="dateOfBirth" class="form-control" value="<?php echo isset($dateOfBirth) ? htmlspecialchars($dateOfBirth) : ''; ?>">
                <?php if (isset($errors['dateOfBirth'])) : ?>
                    <p class="error-message"><?php echo $errors['dateOfBirth']; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                <?php if (isset($errors['address'])) : ?>
                    <p class="error-message"><?php echo $errors['address']; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control">
                <?php if (isset($errors['password'])) : ?>
                    <p class="error-message"><?php echo $errors['password']; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirmPassword" class="form-control">
                <?php if (isset($errors['confirmPassword'])) : ?>
                    <p class="error-message"><?php echo $errors['confirmPassword']; ?></p>
                <?php endif; ?>
            </div>
            <button style="width: 100%; margin-top: 1rem;" type="submit" class="btn btn-primary">Register</button>
        </form>
        <?php if (isset($errors['database'])) : ?>
            <p class="error-message"><?php echo $errors['database']; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>