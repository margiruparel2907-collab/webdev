let addLeave = document.getElementById("addLeave");

let leaveModal = document.getElementById("leaveModal");

let closeLeave = document.getElementById("closeLeave");

let submitLeave = document.getElementById("submitLeave");

let fromDate = document.getElementById("fromDate");

let toDate = document.getElementById("toDate");

let reason = document.getElementById("reason");


/* OPEN MODAL */

addLeave.addEventListener("click", function(){

    leaveModal.style.display = "flex";

});


/* CLOSE MODAL */

closeLeave.addEventListener("click", function(){

    leaveModal.style.display = "none";

});


/* SUBMIT LEAVE */

submitLeave.addEventListener("click", function(){

    if(fromDate.value == "" || toDate.value == "" || reason.value == ""){

        alert("Please fill all the fields.");

        return;

    }


    let leaveBox = document.createElement("div");

    leaveBox.className = "leave-box";


    leaveBox.innerHTML = `
        <p><b>Leave ID :</b> LV004</p>
        <p><b>From :</b> ${fromDate.value}</p>
        <p><b>To :</b> ${toDate.value}</p>
        <p><b>Reason :</b> ${reason.value}</p>
        <p><b>Status :</b> Pending</p>
    `;


    document.querySelector(".leave-container").appendChild(leaveBox);


    alert("Leave submitted successfully!");


    fromDate.value = "";
    toDate.value = "";
    reason.value = "";


    leaveModal.style.display = "none";

});