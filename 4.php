<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reser";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifiez si tous les champs requis sont définis
    if (isset($_POST["name"], $_POST["email"], $_POST["phone"], $_POST["commune"], $_POST["terrain"], $_POST["date"], $_POST["heure"], $_POST["duration"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $commune = $_POST["commune"];
        $terrain = $_POST["terrain"];
        $date = $_POST["date"];
        $time = $_POST["heure"];
        $duration = $_POST["duration"];

        // Check availability
        $availability = checkAvailability($conn, $commune, $terrain, $date, $time, $duration);

        if ($availability["available"]) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO reservations (name, email, phone, reservation_date, reservation_time, duration, commune, terrain) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Correct the bind_param to match the number of variables
            $stmt->bind_param("ssssssss", $name, $email, $phone, $date, $time, $duration, $commune, $terrain);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to confirmation page
                header("Location: confirmation.php?message=Réservation enregistrée avec succès !");
                exit; // Stop further execution
            } else {
                echo "Erreur lors de l'enregistrement de la réservation : " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Désolé, le terrain n'est pas disponible à cette date et heure.";
        }
    } else {
        echo "Tous les champs sont requis.";
    }
}

// Function to check availability
function checkAvailability($conn, $commune, $terrain, $date, $time, $duration) {
    $result = array("available" => false, "end_time" => "");

    // Prepare the SQL statement to check availability
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE commune = ? AND terrain = ? AND reservation_date = ? AND reservation_time <= ? AND reservation_time >= DATE_SUB(?, INTERVAL ? HOUR)");
    $stmt->bind_param("sssssi", $commune, $terrain, $date, $time, $time, $duration);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // Terrain is not available
        $result["available"] = false;
    } else {
        // Terrain is available
        $result["available"] = true;
        $endTime = date('H:i', strtotime($time) + ($duration * 3600));
        $result["end_time"] = $endTime;
    }

    $stmt->close();
    return $result;
}

$conn->close();
?>