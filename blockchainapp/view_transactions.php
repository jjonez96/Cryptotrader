<?php
include_once 'config/db_config.php';

// Fetch transactions from the database
$query = "SELECT * FROM transactions";
$conn = mysqli_connect($server,$user, $pswd, $db);
$result = $conn->query($query);

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Transaction History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">';

if ($result->num_rows > 0) {
    echo "<h2 class='mt-4 text-center'>Transaction History</h2>";
    echo "<a href='index.html' class='btn btn-secondary mt-2'>Home</a>";
    echo "<table class='table table-bordered table-striped mt-4'>
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Receiver</th>
                    <th>Cryptocurrency</th>
                    <th>Amount</th>
                    <th>Timestamp</th>
                    <th>Hash</th>
                </tr>
            </thead>
            <tbody>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['sender']}</td>
                <td>{$row['receiver']}</td>
                <td>{$row['crypto']}</td>
                <td>{$row['amount']}</td>
                <td>{$row['timestamp']}</td>
                <td>{$row['hash']}</td>
              </tr>";
    }
    echo "</tbody>
          </table>";
} else {
    echo "<h2 class='mt-4'>Transaction History</h2>";
    echo "<p class='mt-4'>No transactions found.</p>";
    echo "<a href='index.html' class='btn btn-secondary mt-2'>Home</a>";
}
'</body>
</html>';

// Close the database connection
$conn->close();
?>
