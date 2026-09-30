let faqs = [];

let filteredFaqs = [];

let currentPage = 1;

let recordsPerPage = 5;


let faqList =
    document.getElementById("faqList");

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


/* FETCH FAQ */

async function loadFaqs(){

    try{

        loading.style.display = "block";

        error.innerHTML = "";


        let response =
            await fetch("faqs.json");


        if(!response.ok){

            throw new Error(
                "Unable to load FAQs."
            );

        }


        faqs =
            await response.json();


        loading.style.display = "none";

        applyFilters();

    }

    catch(errorMessage){

        loading.style.display = "none";

        error.innerHTML =
            "Unable to load FAQs. Please try again.";

        console.log(errorMessage);

    }

}


/* SEARCH + FILTER + SORT */

function applyFilters(){

    let searchText =
        searchInput.value.toLowerCase();

    let category =
        categoryFilter.value;


    filteredFaqs =
        faqs.filter(function(faq){

            let matchesSearch =
                faq.question
                    .toLowerCase()
                    .includes(searchText)
                ||
                faq.answer
                    .toLowerCase()
                    .includes(searchText);


            let matchesCategory =
                category == "all" ||
                faq.category == category;


            return matchesSearch &&
                   matchesCategory;

        });


    let sortValue =
        sortSelect.value;


    filteredFaqs.sort(function(a,b){

        if(sortValue == "questionDesc"){

            return b.question.localeCompare(
                a.question
            );

        }

        return a.question.localeCompare(
            b.question
        );

    });


    currentPage = 1;

    renderFaqs();

}


/* RENDER FAQ */

function renderFaqs(){

    faqList.innerHTML = "";


    let totalPages =
        Math.ceil(
            filteredFaqs.length /
            recordsPerPage
        );


    if(totalPages == 0){

        faqList.innerHTML =
            "<p>No FAQs found.</p>";

        pageInfo.innerHTML =
            "Page 0";

        previousButton.disabled = true;

        nextButton.disabled = true;

        return;

    }


    let start =
        (currentPage - 1) *
        recordsPerPage;


    let end =
        start + recordsPerPage;


    let pageFaqs =
        filteredFaqs.slice(start, end);


    pageFaqs.map(function(faq){

        let item =
            document.createElement("div");


        item.className =
            "faq-card";


        item.innerHTML = `
            <button class="faq-question">
                ${faq.question}
                <span>+</span>
            </button>

            <div class="faq-answer">
                ${faq.answer}
            </div>

            <div class="faq-category">
                ${faq.category}
            </div>
        `;


        faqList.appendChild(item);

    });


    pageInfo.innerHTML =
        "Page " + currentPage +
        " of " + totalPages;


    previousButton.disabled =
        currentPage == 1;


    nextButton.disabled =
        currentPage == totalPages;

}


/* FAQ COLLAPSE */

faqList.addEventListener(
    "click",
    function(event){

        if(
            event.target.classList
                .contains("faq-question")
        ){

            let button =
                event.target;

            let item =
                button.parentElement;

            item.classList.toggle("active");


            let symbol =
                button.querySelector("span");


            if(
                item.classList
                    .contains("active")
            ){

                symbol.innerHTML = "-";

            }
            else{

                symbol.innerHTML = "+";

            }

        }

    }
);


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

            renderFaqs();

        }

    }
);


/* NEXT */

nextButton.addEventListener(
    "click",
    function(){

        let totalPages =
            Math.ceil(
                filteredFaqs.length /
                recordsPerPage
            );


        if(currentPage < totalPages){

            currentPage++;

            renderFaqs();

        }

    }
);


/* START */

loadFaqs();