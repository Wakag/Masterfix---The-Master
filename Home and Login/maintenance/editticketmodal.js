// Function to populate and open the modal for ticket resubmission
function openEditModal(ticketID, ticketTitle, ticketCategory) {
    // Get modal elements
    const modal = document.getElementById('editTicketModal');
    const ticketTitleInput = document.getElementById('ticketTitle');
    const ticketIdInput = document.getElementById('ticketId');
    const ticketCategoryInput = document.getElementById('ticketCategory');

    // Set values in modal inputs
    ticketTitleInput.value = `${ticketTitle}, Resubmission of Ticket number ${ticketID}`;
    ticketIdInput.value = ticketID; // Hidden input to store TicketID
    ticketCategoryInput.value = ticketCategory;

    // Show the modal
    modal.style.display = 'block';
}

// Close modal when user clicks the close button
document.querySelector('.close').onclick = function() {
    document.getElementById('editTicketModal').style.display = 'none';
};