function menuHider() {
    var x = document.getElementById("myTopnav");
    var dropdowns = document.getElementsByClassName("hidden");
    
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
    var dropdowns = document.getElementsByClassName("hidden");
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
