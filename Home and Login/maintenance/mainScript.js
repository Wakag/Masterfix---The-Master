function toggleNavbar() {
  document.body.classList.toggle('open-navbar');
}
// Function to toggle dropdown visibility
function toggleDropdown() {
  const dropdown = document.getElementById("options");
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}
  
// Function to close the dropdown if clicked outside of it
window.onclick = function(event) {
  if (!event.target.matches('#trigger-popup')) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    for (let i = 0; i < dropdowns.length; i++) {
      const openDropdown = dropdowns[i];
      if (openDropdown.style.display === "block") {
        openDropdown.style.display = "none";
      }
    }
  }
}



