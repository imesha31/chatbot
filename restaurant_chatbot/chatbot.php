<?php
// Start session
session_start();

// Include database connection
require_once 'db_setup.php';

// Get message from POST request
$message = isset($_POST['message']) ? $_POST['message'] : '';

// Initialize response
$response = [];

// Check if user name is saved in session
if (!isset($_SESSION['user_name'])) {
    // This is likely the first message, so save the name
    $_SESSION['user_name'] = $message;
    $response[] = "Nice to meet you, " . htmlspecialchars($_SESSION['user_name']) . "! How can I help you today? You can ask about our menu, location, hours, or make a reservation.";
} else {
    // Process user message
    $lowerMessage = strtolower($message);
    
    // Check for menu-related keywords
    if (strpos($lowerMessage, 'menu') !== false || 
        strpos($lowerMessage, 'food') !== false || 
        strpos($lowerMessage, 'eat') !== false ||
        strpos($lowerMessage, 'dish') !== false) {
        
        // Get menu items from database
        $conn = getDbConnection();
        $sql = "SELECT * FROM menu_items ORDER BY category, name";
        $result = $conn->query($sql);
        
        $menuItems = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $menuItems[] = $row;
            }
        }
        $conn->close();
        
        $response[] = "Here's our current menu, " . htmlspecialchars($_SESSION['user_name']) . ":";
        $response[] = ['type' => 'menu', 'items' => $menuItems];
        
    } 
    // Check for reservation-related keywords
    else if (strpos($lowerMessage, 'reservation') !== false || 
             strpos($lowerMessage, 'book') !== false || 
             strpos($lowerMessage, 'table') !== false) {
        
        $response[] = "I'd be happy to help you make a reservation, " . htmlspecialchars($_SESSION['user_name']) . ". Please provide the following details:";
        $response[] = "1. Date (e.g., April 30, 2025)<br>2. Time<br>3. Number of people<br>4. Any special requests";
        
    } 
    // Check for location-related keywords
    else if (strpos($lowerMessage, 'location') !== false || 
             strpos($lowerMessage, 'address') !== false || 
             strpos($lowerMessage, 'where') !== false) {
        
        $response[] = "We're located at 123 Gourmet Street, Foodie City. Here's a map:";
        $response[] = "<div class='embed-responsive embed-responsive-16by9 mt-2'>
                        <iframe src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDAyJzI3LjIiTiA3NMKwMDAnMTEuMyJX!5e0!3m2!1sen!2sus!4v1556736782194!5m2!1sen!2sus' width='100%' height='200' frameborder='0' style='border:0; border-radius:5px;' allowfullscreen></iframe>
                      </div>";
        
    } 
    // Check for hours-related keywords
    else if (strpos($lowerMessage, 'hours') !== false || 
             strpos($lowerMessage, 'open') !== false || 
             strpos($lowerMessage, 'close') !== false ||
             strpos($lowerMessage, 'time') !== false) {
        
        $response[] = "Our hours of operation are:<br>
                      Monday - Thursday: 11:00 AM - 10:00 PM<br>
                      Friday - Saturday: 11:00 AM - 11:00 PM<br>
                      Sunday: 12:00 PM - 9:00 PM";
        
    }
    // Check for greeting keywords
    else if (strpos($lowerMessage, 'hi') !== false || 
             strpos($lowerMessage, 'hello') !== false || 
             strpos($lowerMessage, 'hey') !== false) {
        
        $response[] = "Hello again, " . htmlspecialchars($_SESSION['user_name']) . "! How can I help you today?";
        
    }
    // Check for thank you keywords
    else if (strpos($lowerMessage, 'thank') !== false || 
             strpos($lowerMessage, 'thanks') !== false) {
        
        $response[] = "You're welcome, " . htmlspecialchars($_SESSION['user_name']) . "! Is there anything else I can help you with?";
        
    }
    // Default response
    else {
        $response[] = "I'm not sure I understand, " . htmlspecialchars($_SESSION['user_name']) . ". You can ask about our menu, make a reservation, check our location or hours of operation.";
    }
}

// Send response
echo json_encode($response);
?>
