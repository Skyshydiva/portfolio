function change(id, img){
    "use strict";
    document.getElementById(id).src = img;
}

//Create an array of images 
var imageArray = ["../assets/img/City_life.png", "../assets/img/AutumnBaltimoreCounty.png", "../assets/img/baltimore_overview.png"];

//Save total size of array to variable arraySize
var arraySize = imageArray.length;



var x = 1; //Used to count up to arraySize

//Function runit play slideshow when called 
function runit() {
    //Set image to next picture in image array
    document.getElementById('slideshow').src = imageArray[x];

    //Increase count by 1
    x++;

    //If count has reached the last index of imageArray than set count back to starting index.
    if (x === arraySize) {
        x = 0;
    }
}

//Set wait time between slides in milliseconds 
var myTimmer = setInterval(runit, 1500);

//clearInterval(myTimmer);

function stopSlideShow() {
    "use strict";
    clearInterval(myTimmer);
}//end of function to stop the slide show

function validateForm(){
    "use strict";
    var isvalid = true;

    if(document.getElementById("name").value == ""){
        document.getElementById("name").style.borderColor = "red";
        document.getElementById("name").style.backgroundColor = "pink";
        document.getElementById("error1").innerHTML= "Enter Your Name";
    }
    else{
        document.getElementById("name").style = null;
    }

    if(document.getElementById("visited").value == ""){
        document.getElementById("visited").style.borderColor = "red";
        document.getElementById("errorMessage").innerHTML= "Enter a valid date";
    }
    else{
        document.getElementById("visited").style = null;
    }

    if(document.getElementById("state").value == ""){
        document.getElementById("state").style.borderColor = "red";
        document.getElementById("error3").innerHTML= "Choose a state";
    }
    else{
        document.getElementById("state").style = null;
    }

    if(document.getElementById("name").value == "" || document.getElementById("visited").value == "" || document.getElementById("state").value == ""){
        isvalid = false;
    }
    else{
        isvalid = true;
    }

    return (isvalid);
}

function menuHider() {
    var x = document.getElementById("myTopnav");
    var dropdowns = document.getElementsByClassName("dropdown-content");
    
    // Close all dropdowns initially
    for (var i = 0; i < dropdowns.length; i++) {
        dropdowns[i].style.display = "none";
    }

    if (x.className.indexOf("responsive") === -1) {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

function toggleDropdown(index) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var currentDropdown = dropdowns[index];

    // Close all dropdowns initially
    for (var i = 0; i < dropdowns.length; i++) {
        dropdowns[i].style.display = "none";
    }

    // Toggle the current dropdown
    if (currentDropdown.style.display === "block") {
        currentDropdown.style.display = "none";
    } else {
        currentDropdown.style.display = "block";
    }
}