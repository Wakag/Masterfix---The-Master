function openModal() {
    document.getElementById("taskModal").style.display = "block";
    
}

function closeModal() {
    document.getElementById("taskModal").style.display = "none";
}


function confirmAction(message) {
    return confirm(message);
}
