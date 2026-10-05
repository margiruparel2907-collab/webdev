<!DOCTYPE html>
<html>

<head>

    <title>Student Registration</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="registration.css">

</head>

<body>

    <div class="registration-container">

        <h1>Student Registration</h1>

        <p class="subtitle">
            Create your student account
        </p>


        <form method="POST" action="process.php">

            <!-- NAME -->

            <label for="name">
                Full Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
            >

            <br><br>


            <!-- EMAIL -->

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
            >

            <br><br>


            <!-- MOBILE -->

            <label for="mobile">
                Mobile Number
            </label>

            <input
                type="tel"
                id="mobile"
                name="mobile"
                placeholder="Enter 10-digit mobile number"
            >

            <br><br>


            <!-- PASSWORD -->

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
            >

            <br><br>


            <!-- CONFIRM PASSWORD -->

            <label for="confirmPassword">
                Confirm Password
            </label>

            <input
                type="password"
                id="confirmPassword"
                name="confirmPassword"
                placeholder="Re-enter password"
            >

            <br><br>


            <!-- COURSE -->

            <label for="course">
                Course
            </label>

            <select id="course" name="course">

                <option value="">
                    Select Course
                </option>

                <option value="Computer Engineering">
                    Computer Engineering
                </option>

                <option value="Information Technology">
                    Information Technology
                </option>

                <option value="Mechanical Engineering">
                    Mechanical Engineering
                </option>

                <option value="Electrical Engineering">
                    Electrical Engineering
                </option>

            </select>

            <br><br>


            <!-- YEAR -->

            <label for="year">
                Year
            </label>

            <select id="year" name="year">

                <option value="">
                    Select Year
                </option>

                <option value="1">
                    1st Year
                </option>

                <option value="2">
                    2nd Year
                </option>

                <option value="3">
                    3rd Year
                </option>

                <option value="4">
                    4th Year
                </option>

            </select>

            <br><br>


            <!-- GENDER -->

            <label>
                Gender
            </label>

            <br>

            <input
                type="radio"
                name="gender"
                value="Male"
            >
            Male

            <input
                type="radio"
                name="gender"
                value="Female"
            >
            Female

            <input
                type="radio"
                name="gender"
                value="Other"
            >
            Other

            <br><br>


            <!-- TERMS -->

            <input
                type="checkbox"
                id="terms"
                name="terms"
                value="accepted"
            >

            <label for="terms">
                I accept the Terms and Conditions
            </label>

            <br><br>


            <!-- REGISTER -->

            <button type="submit">
                Register
            </button>

        </form>

    </div>

</body>

</html>