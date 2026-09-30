let events = [];

let filteredEvents = [];

let currentPage = 1;

let recordsPerPage = 5;


let eventList =
    document.getElementById("eventList");

let searchInput =
    document.getElementById("searchInput");

let categoryFilter =
    document.getElementById("categoryFilter");

let sortSelect =
    document.getElementById("sortSelect");

let previousButton =
    document.getElementById("previousButton");

let nextButton =
    document.getElementById("nextButton");

let pageInfo =
    document.getElementById("pageInfo");

let loading =
    document.getElementById("loading");

let error =
    document.getElementById("error");


/* FETCH EVENTS */

async function loadEvents(){

    try{

        loading.style.display = "block";

        error.innerHTML = "";

        let response =
            await fetch("events.json");


        if(!response.ok){

            throw new Error("Unable to load events.");

        }


        events =
            await response.json();


        loading.style.display = "none";

        applyFilters();

    }

    catch(errorMessage){

        loading.style.display = "none";

        error.innerHTML =
            "Unable to load events. Please try again.";

        console.log(errorMessage);

    }

}


/* SEARCH + FILTER + SORT */

function applyFilters(){

    let searchText =
        searchInput.value.toLowerCase();

    let category =
        categoryFilter.value;


    filteredEvents =
        events.filter(function(event){

            let matchesSearch =
                event.title.toLowerCase().includes(searchText);


            let matchesCategory =
                category == "all" ||
                event.category == category;


            return matchesSearch &&
                   matchesCategory;

        });


    /* SORT */

    let sortValue =
        sortSelect.value;


    filteredEvents.sort(function(a,b){

        if(sortValue == "nameAsc"){

            return a.title.localeCompare(b.title);

        }

        if(sortValue == "nameDesc"){

            return b.title.localeCompare(a.title);

        }

        if(sortValue == "dateDesc"){

            return new Date(b.date) - new Date(a.date);

        }

        return new Date(a.date) - new Date(b.date);

    });


    currentPage = 1;

    renderEvents();

}


/* RENDER EVENTS */

function renderEvents(){

    eventList.innerHTML = "";


    let totalPages =
        Math.ceil(
            filteredEvents.length / recordsPerPage
        );


    if(totalPages == 0){

        eventList.innerHTML =
            "<p>No events found.</p>";

        pageInfo.innerHTML = "Page 0";

        previousButton.disabled = true;

        nextButton.disabled = true;

        return;

    }


    let start =
        (currentPage - 1) * recordsPerPage;


    let end =
        start + recordsPerPage;


    let pageEvents =
        filteredEvents.slice(start, end);


    pageEvents.map(function(event){

        let card =
            document.createElement("div");


        card.className = "data-card";


        card.innerHTML = `
            <h3>${event.title}</h3>

            <p><b>Category:</b> ${event.category}</p>

            <p><b>Date:</b> ${event.date}</p>

            <p><b>Location:</b> ${event.location}</p>
        `;


        eventList.appendChild(card);

    });


    pageInfo.innerHTML =
        "Page " + currentPage +
        " of " + totalPages;


    previousButton.disabled =
        currentPage == 1;


    nextButton.disabled =
        currentPage == totalPages;

}


/* SEARCH */

searchInput.addEventListener(
    "input",
    applyFilters
);


/* FILTER */

categoryFilter.addEventListener(
    "change",
    applyFilters
);


/* SORT */

sortSelect.addEventListener(
    "change",
    applyFilters
);


/* PREVIOUS */

previousButton.addEventListener(
    "click",
    function(){

        if(currentPage > 1){

            currentPage--;

            renderEvents();

        }

    }
);


/* NEXT */

nextButton.addEventListener(
    "click",
    function(){

        let totalPages =
            Math.ceil(
                filteredEvents.length /
                recordsPerPage
            );


        if(currentPage < totalPages){

            currentPage++;

            renderEvents();

        }

    }
);


/* START */

loadEvents();