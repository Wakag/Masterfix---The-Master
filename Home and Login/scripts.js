
function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}

document.getElementById("openFormButton").addEventListener("click", function() {
  document.getElementById("myForm").style.display = "block";
  document.getElementById("main-overlay").style.display = "block"; // Show the overlay
});

// Close the form when the cancel button is clicked
document.getElementById("closeFormButton").addEventListener("click", function() {
  document.getElementById("myForm").style.display = "none";
  document.getElementById("main-overlay").style.display = "none"; // Hide the overlay
});

// Optional: Close the form and overlay when clicking outside the form
document.getElementById("main-overlay").addEventListener("click", function() {
  document.getElementById("myForm").style.display = "none";
  document.getElementById("main-overlay").style.display = "none"; // Hide the overlay
});
