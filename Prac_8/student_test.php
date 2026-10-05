<?php

require "db.php";

$email = "margi@gmail.com";

$sql = "SELECT * FROM students WHERE email = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$email]);

$student = $stmt->fetch();

if ($student) {

    echo "Student Found<br><br>";

    echo "Name: " . htmlspecialchars($student["name"]) . "<br>";

    echo "Email: " . htmlspecialchars($student["email"]) . "<br>";

    echo "Course: " . htmlspecialchars($student["course"]) . "<br>";

} else {

    echo "Student not found.";

}

?>