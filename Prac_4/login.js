let loginButton = document.getElementById("loginButton");

let loginModal = document.getElementById("loginModal");

let closeLogin = document.getElementById("closeLogin");

let okLogin = document.getElementById("okLogin");

let loginTitle = document.getElementById("loginTitle");

let loginMessage = document.getElementById("loginMessage");

let username = document.getElementById("username");

let password = document.getElementById("password");

let usernameError = document.getElementById("usernameError");

let passwordError = document.getElementById("passwordError");


/* LOGIN */

loginButton.addEventListener("click", function(){

    let enteredUsername = username.value.trim();
    let enteredPassword = password.value;

    usernameError.innerHTML = "";
    passwordError.innerHTML = "";

    let valid = true;


    /* USERNAME REGEX */

    let usernamePattern = /^[A-Za-z0-9_]{4,20}$/;

    if(enteredUsername == ""){

        usernameError.innerHTML = "Username is required.";
        valid = false;

    }
    else if(!usernamePattern.test(enteredUsername)){

        usernameError.innerHTML =
            "Username must contain 4-20 letters, numbers or underscores.";

        valid = false;

    }


    /* PASSWORD */

    if(enteredPassword == ""){

        passwordError.innerHTML = "Password is required.";
        valid = false;

    }
    else if(enteredPassword.length < 5){

        passwordError.innerHTML =
            "Password must be at least 5 characters.";

        valid = false;

    }


    /* STOP IF INPUT IS INVALID */

    if(!valid){
        return;
    }


    /* LOGIN SUCCESS */

    loginTitle.innerHTML = "Login Successful!";

    loginMessage.innerHTML =
        "Welcome to Student Hub.";

    loginModal.style.display = "flex";


    okLogin.onclick = function(){

        window.location.href = "dashboard.html";

    };

});


/* CLOSE BUTTON */

closeLogin.addEventListener("click", function(){

    loginModal.style.display = "none";

});


/* CLOSE OUTSIDE MODAL */

loginModal.addEventListener("click", function(event){

    if(event.target === loginModal){

        loginModal.style.display = "none";

    }

});