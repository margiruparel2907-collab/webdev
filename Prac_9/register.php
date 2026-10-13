
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>StudentHub</h1>
        <h2>Create Account</h2>
        <p>Register to get started.</p>

        <form action="process.php" method="POST">

            <label for="name">Full Name</label>
            <input type="text" id="name" name="name"
                   required maxlength="100"
                   pattern="[A-Za-z ]{2,100}"
                   title="Enter a name using letters and spaces only">

            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   required minlength="3" maxlength="30"
                   pattern="[A-Za-z0-9_]+"
                   title="Use letters, numbers and underscores">

            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   required maxlength="150">

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   required minlength="8"
                   autocomplete="new-password">

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password"
                   name="confirm_password" required minlength="8"
                   autocomplete="new-password">

            <label class="terms">
                <input type="checkbox" name="terms" value="yes" required>
                I accept the terms and conditions.
            </label>

            <button type="submit">Register</button>
        </form>

        <p class="note">Already registered? This demo is for Practical 9.</p>
    </div>

    <script>
        const form = document.querySelector("form");
        const password = document.getElementById("password");
        const confirmPassword =
            document.getElementById("confirm_password");

        form.addEventListener("submit", function (event) {
            if (password.value !== confirmPassword.value) {
                event.preventDefault();
                alert("Passwords do not match!");
                confirmPassword.focus();
            }
        });
    </script>

</body>
</html>
