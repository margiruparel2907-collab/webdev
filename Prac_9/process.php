<?php

require "db.php";

/* ACCEPT POST REQUEST ONLY */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit;
}


/* RECEIVE AND CLEAN INPUTS */

$name = trim($_POST["name"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";
$terms = $_POST["terms"] ?? "";


/* ERROR ARRAY */

$errors = [];


/* NAME VALIDATION */

if ($name === "") {
    $errors[] = "Name is required.";
} elseif (!preg_match("/^[A-Za-z ]{2,100}$/", $name)) {
    $errors[] = "Name must contain only letters and spaces.";
}


/* USERNAME VALIDATION */

if ($username === "") {
    $errors[] = "Username is required.";
} elseif (!preg_match("/^[A-Za-z0-9_]{3,30}$/", $username)) {
    $errors[] = "Username must be 3-30 characters using letters, numbers or underscores.";
}


/* EMAIL VALIDATION */

if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email address.";
}


/* PASSWORD VALIDATION */

if ($password === "") {
    $errors[] = "Password is required.";
} elseif (
    strlen($password) < 8 ||
    strlen($password) > 72 ||
    !preg_match("/[A-Za-z]/", $password) ||
    !preg_match("/[0-9]/", $password)
) {
    $errors[] = "Password must be 8-72 characters and contain at least one letter and one number.";
}


/* CONFIRM PASSWORD */

if ($confirmPassword === "") {
    $errors[] = "Please confirm your password.";
} elseif ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}


/* TERMS VALIDATION */

if ($terms !== "yes") {
    $errors[] = "Please accept the Terms and Conditions.";
}


/* CHECK FOR DUPLICATE USERNAME OR EMAIL */

if (empty($errors)) {

    $check = $conn->prepare(
        "SELECT username, email FROM users
         WHERE username = ? OR email = ?"
    );

    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->bind_result($existingUsername, $existingEmail);

    while ($check->fetch()) {

        if ($existingUsername === $username) {
            $errors[] = "This username is already registered.";
        }

        if ($existingEmail === $email) {
            $errors[] = "This email is already registered.";
        }
    }

    $check->close();
}


/* INSERT NEW USER */

if (empty($errors)) {

    /* HASH PASSWORD */

    $passwordHash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    if ($passwordHash === false) {
        $errors[] = "Unable to process the password. Please try again.";
    } else {

        try {

            $stmt = $conn->prepare(
                "INSERT INTO users
                 (name, username, email, password_hash)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $username,
                $email,
                $passwordHash
            );

            $stmt->execute();
            $stmt->close();

            $success = true;

        } catch (mysqli_sql_exception $e) {

            /* Log details privately, not to the visitor. */
            error_log("Registration database error: " . $e->getMessage());

            if ($e->getCode() === 1062) {
                $errors[] = "That username or email is already registered.";
            } else {
                $errors[] = "Registration could not be completed. Please try again.";
            }
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">

        <?php if (!empty($success)) { ?>

            <h1>Registration Successful!</h1>

            <p>Your account has been created successfully.</p>

        <?php } else { ?>

            <h1>Registration Failed</h1>

            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li>
                        <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
                    </li>
                <?php } ?>
            </ul>

        <?php } ?>

        <p>
            <a href="register.php">Back to Registration</a>
        </p>

    </div>

</body>
</html>