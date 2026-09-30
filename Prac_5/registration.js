let registrationForm =
    document.getElementById("registrationForm");


let name =
    document.getElementById("name");

let email =
    document.getElementById("email");

let mobile =
    document.getElementById("mobile");

let password =
    document.getElementById("password");

let confirmPassword =
    document.getElementById("confirmPassword");

let course =
    document.getElementById("course");

let year =
    document.getElementById("year");

let terms =
    document.getElementById("terms");


let nameError =
    document.getElementById("nameError");

let emailError =
    document.getElementById("emailError");

let mobileError =
    document.getElementById("mobileError");

let passwordError =
    document.getElementById("passwordError");

let confirmPasswordError =
    document.getElementById("confirmPasswordError");

let courseError =
    document.getElementById("courseError");

let yearError =
    document.getElementById("yearError");

let genderError =
    document.getElementById("genderError");

let termsError =
    document.getElementById("termsError");

let successMessage =
    document.getElementById("successMessage");


/* FORM SUBMIT */

registrationForm.addEventListener("submit", function(event){

    event.preventDefault();


    /* CLEAR OLD MESSAGES */

    nameError.innerHTML = "";
    emailError.innerHTML = "";
    mobileError.innerHTML = "";
    passwordError.innerHTML = "";
    confirmPasswordError.innerHTML = "";
    courseError.innerHTML = "";
    yearError.innerHTML = "";
    genderError.innerHTML = "";
    termsError.innerHTML = "";
    successMessage.innerHTML = "";


    let valid = true;


    /* REGEX */

    let namePattern =
        /^[A-Za-z ]{2,50}$/;

    let emailPattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    let mobilePattern =
        /^[0-9]{10}$/;

    let passwordPattern =
        /^(?=.*[A-Za-z])(?=.*\d).{6,20}$/;


    /* NAME */

    if(name.value.trim() == ""){

        nameError.innerHTML =
            "Name is required.";

        valid = false;

    }
    else if(!namePattern.test(name.value.trim())){

        nameError.innerHTML =
            "Enter a valid name.";

        valid = false;
    }


    /* EMAIL */

    if(email.value.trim() == ""){

        emailError.innerHTML =
            "Email is required.";

        valid = false;

    }
    else if(!emailPattern.test(email.value.trim())){

        emailError.innerHTML =
            "Enter a valid email.";

        valid = false;
    }


    /* MOBILE */

    if(mobile.value.trim() == ""){

        mobileError.innerHTML =
            "Mobile number is required.";

        valid = false;

    }
    else if(!mobilePattern.test(mobile.value.trim())){

        mobileError.innerHTML =
            "Enter a valid 10-digit mobile number.";

        valid = false;
    }


    /* PASSWORD */

    if(password.value == ""){

        passwordError.innerHTML =
            "Password is required.";

        valid = false;

    }
    else if(!passwordPattern.test(password.value)){

        passwordError.innerHTML =
            "Password must contain letters and numbers.";

        valid = false;
    }


    /* CONFIRM PASSWORD */

    if(confirmPassword.value == ""){

        confirmPasswordError.innerHTML =
            "Please confirm your password.";

        valid = false;

    }
    else if(password.value != confirmPassword.value){

        confirmPasswordError.innerHTML =
            "Passwords do not match.";

        valid = false;
    }


    /* COURSE */

    if(course.value == ""){

        courseError.innerHTML =
            "Please select a course.";

        valid = false;
    }


    /* YEAR */

    if(year.value == ""){

        yearError.innerHTML =
            "Please select your year.";

        valid = false;
    }


    /* GENDER */

    let gender =
        document.querySelector(
            'input[name="gender"]:checked'
        );

    if(!gender){

        genderError.innerHTML =
            "Please select your gender.";

        valid = false;
    }


    /* TERMS */

    if(!terms.checked){

        termsError.innerHTML =
            "You must accept the terms and conditions.";

        valid = false;
    }


    /* STOP IF INVALID */

    if(!valid){

        return;

    }


    /* SUCCESS */

    successMessage.innerHTML =
        "Registration successful!";

});