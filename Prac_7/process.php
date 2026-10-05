<?php

/* CHECK POST */

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    echo "<h2>Invalid request.</h2>";

    echo '<a href="registration.php">Back to Registration</a>';

    exit;
}


/* GET DATA */

$name = trim($_POST["name"] ?? "");

$email = trim($_POST["email"] ?? "");

$mobile = trim($_POST["mobile"] ?? "");

$password = $_POST["password"] ?? "";

$confirmPassword = $_POST["confirmPassword"] ?? "";

$course = trim($_POST["course"] ?? "");

$year = trim($_POST["year"] ?? "");

$gender = trim($_POST["gender"] ?? "");

$terms = isset($_POST["terms"]);


/* SANITIZATION */

$name = preg_replace("/\s+/", " ", $name);

$email = filter_var($email, FILTER_SANITIZE_EMAIL);

$mobile = preg_replace("/[^0-9]/", "", $mobile);

$course = htmlspecialchars($course, ENT_QUOTES, "UTF-8");

$year = htmlspecialchars($year, ENT_QUOTES, "UTF-8");

$gender = htmlspecialchars($gender, ENT_QUOTES, "UTF-8");


/* ERROR ARRAY */

$errors = [];


/* REGEX */

$namePattern = "/^[A-Za-z ]{2,50}$/";

$mobilePattern = "/^[0-9]{10}$/";

$passwordPattern = "/^(?=.*[A-Za-z])(?=.*\d).{6,20}$/";


/* NAME VALIDATION */

if ($name == "") {

    $errors[] = "Name is required.";

}
else if (!preg_match($namePattern, $name)) {

    $errors[] = "Enter a valid name.";

}


/* EMAIL VALIDATION */

if ($email == "") {

    $errors[] = "Email is required.";

}
else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errors[] = "Enter a valid email address.";

}


/* MOBILE VALIDATION */

if ($mobile == "") {

    $errors[] = "Mobile number is required.";

}
else if (!preg_match($mobilePattern, $mobile)) {

    $errors[] = "Enter a valid 10-digit mobile number.";

}


/* PASSWORD VALIDATION */

if ($password == "") {

    $errors[] = "Password is required.";

}
else if (!preg_match($passwordPattern, $password)) {

    $errors[] =
        "Password must contain letters and numbers and be 6-20 characters long.";

}


/* CONFIRM PASSWORD */

if ($confirmPassword == "") {

    $errors[] = "Please confirm your password.";

}
else if ($password != $confirmPassword) {

    $errors[] = "Passwords do not match.";

}


/* COURSE */

$allowedCourses = [
    "Computer Engineering",
    "Information Technology",
    "Mechanical Engineering",
    "Electrical Engineering"
];

if (!in_array($course, $allowedCourses, true)) {

    $errors[] = "Please select a valid course.";

}


/* YEAR */

$allowedYears = ["1", "2", "3", "4"];

if (!in_array($year, $allowedYears, true)) {

    $errors[] = "Please select a valid year.";

}


/* GENDER */

$allowedGenders = [
    "Male",
    "Female",
    "Other"
];

if (!in_array($gender, $allowedGenders, true)) {

    $errors[] = "Please select a valid gender.";

}


/* TERMS */

if (!$terms) {

    $errors[] = "Please accept the Terms and Conditions.";

}


/* DISPLAY ERRORS */

if (!empty($errors)) {

    echo "<!DOCTYPE html>";

    echo "<html>";

    echo "<head>";

    echo "<title>Registration Error</title>";

    echo "</head>";

    echo "<body>";

    echo "<h2>Registration Failed</h2>";

    echo "<ul>";

    foreach ($errors as $error) {

        echo "<li>" .
             htmlspecialchars($error, ENT_QUOTES, "UTF-8") .
             "</li>";

    }

    echo "</ul>";

    echo '<a href="registration.php">Go Back</a>';

    echo "</body>";

    echo "</html>";

    exit;
}


/* PASSWORD HASH */

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/* CSV FILE */

$fileName = "registrations.csv";


/* CHECK IF FILE EXISTS / EMPTY */

$fileExists = file_exists($fileName);

$fileEmpty = !$fileExists || filesize($fileName) == 0;


/* OPEN CSV */

$file = fopen($fileName, "a");


if ($file === false) {

    echo "<h2>Error: Unable to open CSV file.</h2>";

    exit;
}


/* ADD HEADER */

if ($fileEmpty) {

    fputcsv(
        $file,
        [
            "Name",
            "Email",
            "Mobile",
            "Password",
            "Course",
            "Year",
            "Gender"
        ]
    );

}


/* ADD RECORD */

fputcsv(
    $file,
    [
        $name,
        $email,
        $mobile,
        $passwordHash,
        $course,
        $year,
        $gender
    ]
);


/* CLOSE FILE */

fclose($file);


/* SUCCESS */

?>

<!DOCTYPE html>
<html>

<head>

    <title>Registration Successful</title>

</head>

<body>

    <h2>
        Registration Successful!
    </h2>

    <p>
        Your details have been saved successfully.
    </p>

    <a href="registration.php">
        Register Another Student
    </a>

</body>

</html>