<?php
include_once 'config/db_config.php';

function generateTransactionHash($sender, $receiver, $amount, $timestamp, $crypto)
{
    $data = $sender . $receiver . $amount . $timestamp . $crypto;
    return hash('sha256', $data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $sender = $_POST["sender"];
    $receiver = $_POST["receiver"];
    $amount = $_POST["amount"];
    $crypto = $_POST["crypto"];

    // Validate the form data (you can add more validation)

    // Generate a timestamp for the transaction
    $timestamp = date("Y-m-d H:i:s");

    // Generate a hash for the transaction
    $hash = generateTransactionHash($sender, $receiver, $amount, $timestamp, $crypto);  

    // Insert the transaction into the blockchain
    $sql = "INSERT INTO transactions (sender, receiver, amount, timestamp, hash, crypto) VALUES ('$sender', '$receiver', $amount, '$timestamp', '$hash', '$crypto')";
    $conn = mysqli_connect($server,$user, $pswd, $db);
    
    if ($conn->query($sql) === TRUE) {
        echo "<center><h1>Transaction successful! Redirecting...</h1><center>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.html';
                }, 3000);
              </script>";
        exit(); // Ensure that no further content is sent
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
