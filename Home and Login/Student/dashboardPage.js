//script for the delete icons
document.addEventListener('DOMContentLoaded', function () {
    // Select all delete icons
    const deleteButtons = document.querySelectorAll('#trash');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Get the parent ticket item element
            const ticketItem = this.closest('.ticket-item');

            // Optionally, you can confirm before deleting
            if (confirm('Are you sure you want to remove this ticket from the list?')) {
                // Remove the ticket item from the DOM
                ticketItem.remove();
                ticketItem.nextElementSibling.remove(); // Remove the separator
            }
        });
    });
});

//script for the edit icons
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('#edit');
    const modal = document.getElementById('editTicketModal');
    const closeBtn = document.querySelector('.modal .close');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Show the modal
            modal.style.display = 'block';
        });
    });

    // Close the modal
    closeBtn.onclick = function () {
        modal.style.display = 'none';
    }

    // Close the modal if user clicks outside of it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
});
//script for the segmented buttons
// Get all buttons within the segmented-buttons container
const buttons = document.querySelectorAll('.segment-btn');

// Add click event listener to each button
buttons.forEach(button => {
    button.addEventListener('click', function () {
        // Remove 'active' class from all buttons
        buttons.forEach(btn => btn.classList.remove('active'));
        // Add 'active' class to the clicked button
        this.classList.add('active');
    });
});
//script for the spinner loader on the dashboard 
window.onload = function () {
    // Show spinner when page is loading
    document.getElementById("loadingSpinner").style.display = "block";

    // Hide spinner after a delay or when the content has loaded
    // For demo purposes, I'm using a timeout, but you should hide it after fetching data
    setTimeout(function () {
        document.getElementById("loadingSpinner").style.display = "none";
    }, 100); // Adjust timing as needed based on fetch time
};