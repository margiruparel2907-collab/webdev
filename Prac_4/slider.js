let images = [
    "31618836056.jpg",
    "campus2.jpg",
    "campus3.jpg"
];

let currentImage = 0;

let sliderImage = document.getElementById("sliderImage");

let previous = document.getElementById("previous");

let next = document.getElementById("next");


/* SHOW IMAGE */

function showImage(){

    sliderImage.src = images[currentImage];

}


/* NEXT BUTTON */

next.addEventListener("click", function(){

    currentImage++;

    if(currentImage >= images.length){
        currentImage = 0;
    }

    showImage();

});


/* PREVIOUS BUTTON */

previous.addEventListener("click", function(){

    currentImage--;

    if(currentImage < 0){
        currentImage = images.length - 1;
    }

    showImage();

});