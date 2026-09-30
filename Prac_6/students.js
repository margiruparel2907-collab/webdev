let students = [];

let filteredStudents = [];

let currentPage = 1;

let recordsPerPage = 5;


let studentList =
    document.getElementById("studentList");

let searchInput =
    document.getElementById("searchInput");

let courseFilter =
    document.getElementById("courseFilter");

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


/* FETCH STUDENTS */

async function loadStudents(){

    try{

        loading.style.display = "block";

        error.innerHTML = "";


        let response =
            await fetch("students.json");


        if(!response.ok){

            throw new Error(
                "Unable to load students."
            );

        }


        students =
            await response.json();


        loading.style.display = "none";

        applyFilters();

    }

    catch(errorMessage){

        loading.style.display = "none";

        error.innerHTML =
            "Unable to load students. Please try again.";

        console.log(errorMessage);

    }

}


/* FILTER + SEARCH + SORT */

function applyFilters(){

    let searchText =
        searchInput.value.toLowerCase();

    let course =
        courseFilter.value;


    filteredStudents =
        students.filter(function(student){

            let matchesSearch =
                student.name
                    .toLowerCase()
                    .includes(searchText)
                ||
                student.id
                    .toLowerCase()
                    .includes(searchText);


            let matchesCourse =
                course == "all" ||
                student.course == course;


            return matchesSearch &&
                   matchesCourse;

        });


    let sortValue =
        sortSelect.value;


    filteredStudents.sort(function(a,b){

        if(sortValue == "nameDesc"){

            return b.name.localeCompare(a.name);

        }

        if(sortValue == "idAsc"){

            return a.id.localeCompare(b.id);

        }

        if(sortValue == "yearAsc"){

            return a.year - b.year;

        }

        return a.name.localeCompare(b.name);

    });


    currentPage = 1;

    renderStudents();

}


/* RENDER STUDENTS */

function renderStudents(){

    studentList.innerHTML = "";


    let totalPages =
        Math.ceil(
            filteredStudents.length /
            recordsPerPage
        );


    if(totalPages == 0){

        studentList.innerHTML =
            "<p>No students found.</p>";

        pageInfo.innerHTML = "Page 0";

        previousButton.disabled = true;

        nextButton.disabled = true;

        return;

    }


    let start =
        (currentPage - 1) *
        recordsPerPage;


    let end =
        start + recordsPerPage;


    let pageStudents =
        filteredStudents.slice(start, end);


    pageStudents.map(function(student){

        let card =
            document.createElement("div");


        card.className =
            "data-card";


        card.innerHTML = `
            <h3>${student.name}</h3>

            <p><b>Student ID:</b> ${student.id}</p>

            <p><b>Course:</b> ${student.course}</p>

            <p><b>Year:</b> ${student.year}</p>

            <p><b>Division:</b> ${student.division}</p>
        `;


        studentList.appendChild(card);

    });


    pageInfo.innerHTML =
        "Page " + currentPage +
        " of " + totalPages;


    previousButton.disabled =
        currentPage == 1;


    nextButton.disabled =
        currentPage == totalPages;

}


/* EVENTS */

searchInput.addEventListener(
    "input",
    applyFilters
);


courseFilter.addEventListener(
    "change",
    applyFilters
);


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

            renderStudents();

        }

    }
);


/* NEXT */

nextButton.addEventListener(
    "click",
    function(){

        let totalPages =
            Math.ceil(
                filteredStudents.length /
                recordsPerPage
            );


        if(currentPage < totalPages){

            currentPage++;

            renderStudents();

        }

    }
);


/* START */

loadStudents();