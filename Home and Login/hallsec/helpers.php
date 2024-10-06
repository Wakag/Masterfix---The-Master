<?php

require_once('config.php');
$conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

// Function to get hall ID based on hall secretary ID
function get_hall_id($hall_secretary_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT hall_id 
        FROM halls 
        JOIN hallsecretary ON halls.hall_secretary_id = hallsecretary.hall_secretary_id 
        WHERE hallsecretary.hall_secretary_userID = ?");
    $stmt->bind_param("i", $hall_secretary_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hall_id = $result->fetch_assoc()['hall_id'];
    $stmt->close(); // Close the statement
    return $hall_id;
}

function get_hall_name($hall_secretary_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT hall_name 
        FROM halls 
        JOIN hallsecretary ON halls.hall_secretary_id = hallsecretary.hall_secretary_id 
        WHERE hallsecretary.hall_secretary_userID = ?");
    $stmt->bind_param("i", $hall_secretary_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hall_id = $result->fetch_assoc()['hall_name'];
    $stmt->close(); // Close the statement
    return $hall_id;
}
function get_residence_name($warden_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT ResidenceName 
        FROM residence AS r
        JOIN warden AS w ON r.Residence_id = w.ResidenceID
        WHERE w.wardenID = ?");
    $stmt->bind_param("i", $warden_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the residence name from the result
    $residence_name = $result->fetch_assoc()['ResidenceName'];
    
    $stmt->close(); // Close the statement
    return $residence_name;
}


// Function to get the count of tickets based on status
function get_ticket_count_by_status($hall_id, $status) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS count 
        FROM maintenancetickets 
        WHERE Status = ? 
        AND TicketResID IN (SELECT Residence_id FROM residence WHERE res_hall_id = ?)");
    $stmt->bind_param("si", $status, $hall_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close(); // Close the statement
    return $count;
}

// Function to get the total open tickets
function get_total_open_tickets($hall_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS count 
        FROM maintenancetickets 
        WHERE Status IN ('Open', 'Confirmed', 'Requisition') 
        AND TicketResID IN (SELECT Residence_id FROM residence WHERE res_hall_id = ?)");
    $stmt->bind_param("i", $hall_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close(); // Close the statement
    return $count;
}

// Function to get filtered tickets
function get_filtered_tickets($hall_id, $filters) {
    global $conn;

    $priority_filter = $filters['priority'] ?? 'all';
    $priority_condition = '';
    if ($priority_filter === 'high') {
        $priority_condition = "AND (TicketCategory = 'Plumbing' OR TicketCategory = 'Electrical')";
    } elseif ($priority_filter === 'medium') {
        $priority_condition = "AND (TicketCategory != 'Plumbing' AND TicketCategory != 'Electrical')";
    }

    $search_query = $filters['search'] ?? '';
    $search_condition = '';
    if (!empty($search_query)) {
        $search_query = $conn->real_escape_string($search_query);
        $search_condition = "AND (TicketID LIKE '%$search_query%' OR student.FirstName LIKE '%$search_query%' OR residence.ResName LIKE '%$search_query%')";
    }

    $category_filter = $filters['category'] ?? 'all';
    $category_condition = '';
    if ($category_filter !== 'all') {
        $category_condition = "AND TicketCategory = '$category_filter'";
    }

    $stmt = $conn->prepare("
        SELECT TicketID, TicketTitle, TicketCategory, student.FirstName, student.LastName, residence.ResidenceName, RoomNumber, DateCreated
        FROM maintenancetickets
        JOIN residence ON maintenancetickets.TicketResID = residence.Residence_id
        JOIN student ON maintenancetickets.TicketStudentID = student.StudentID
        WHERE TicketResID IN (SELECT Residence_id FROM residence WHERE res_hall_id = ?) And Status in ('Confirmed','Resolved')
        $priority_condition $search_condition $category_condition
        ORDER BY TicketID, DateCreated DESC
        ");
    
    $stmt->bind_param("i", $hall_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tickets = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close(); // Close the statement
    return $tickets;
}

// Function to update ticket status
function update_ticket_status($ticketID, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE maintenancetickets SET Status = ? WHERE TicketID = ?");
    $stmt->bind_param("si", $status, $ticketID);
    $success = $stmt->execute();
    $stmt->close(); // Close the statement
    return $success;
}

?>
