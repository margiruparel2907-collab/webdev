let modal = document.getElementById("certificateModal");

let closeModal = document.getElementById("closeModal");

let certificateImage = document.getElementById("certificateImage");

let modalTitle = document.getElementById("modalTitle");

let viewButtons = document.querySelectorAll(".view-btn");


viewButtons.forEach(function(button){

    button.addEventListener("click", function(){

        let card = button.parentElement;

        let image = card.querySelector("img");

        let title = card.querySelector("h3");

        certificateImage.src = image.src;

        modalTitle.innerHTML = title.innerHTML;

        modal.style.display = "flex";

    });

});


/* CLOSE BUTTON */

closeModal.addEventListener("click", function(){

    modal.style.display = "none";

});


/* CLOSE WHEN CLICKING OUTSIDE */

modal.addEventListener("click", function(event){

    if(event.target === modal){

        modal.style.display = "none";

    }

});