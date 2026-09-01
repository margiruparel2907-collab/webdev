let notification = document.getElementById("notification");

let closeNotification = document.getElementById("closeNotification");

closeNotification.addEventListener("click", function(){

    notification.style.display = "none";

});

let questions = document.querySelectorAll(".faq-question");

questions.forEach(function(question){

    question.addEventListener("click", function(){

        let item = question.parentElement;

        item.classList.toggle("active");

        let symbol = question.querySelector("span");

        if(item.classList.contains("active")){
            symbol.innerHTML = "-";
        }
        else{
            symbol.innerHTML = "+";
        }

    });

});
// THEME

let themeButton = document.getElementById("themeButton");

themeButton.addEventListener("click", function(){

    document.body.classList.toggle("dark");

    if(document.body.classList.contains("dark")){

        themeButton.innerHTML = "☀️";

        localStorage.setItem("theme", "dark");

    }
    else{

        themeButton.innerHTML = "🌙";

        localStorage.setItem("theme", "light");

    }

});
// REMEMBER THEME

let savedTheme = localStorage.getItem("theme");

if(savedTheme == "dark"){

    document.body.classList.add("dark");

    themeButton.innerHTML = "☀️";

}