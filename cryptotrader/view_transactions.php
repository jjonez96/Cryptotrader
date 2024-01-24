<?php
include_once 'config/db_config.php';

// Pagination settings
$records_per_page = 15; // Number of records to show per page

// Fetch total number of transactions
$query_total = "SELECT COUNT(*) as total FROM transactions";
$conn_total = mysqli_connect($server, $user, $pswd, $db);
$result_total = $conn_total->query($query_total);
$total_records = $result_total->fetch_assoc()['total'];

// Calculate the total number of pages
$total_pages = ceil($total_records / $records_per_page);

// Get the current page number
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = $_GET['page'];
} else {
    $current_page = 1;
}

// Ensure the current page is within the valid range
if ($current_page > $total_pages) {
    $current_page = $total_pages;
} elseif ($current_page < 1) {
    $current_page = 1;
}

// Calculate the offset for the query
$offset = ($current_page - 1) * $records_per_page;

// Fetch transactions for the current page in reverse order
$query_page = "SELECT * FROM transactions ORDER BY timestamp DESC LIMIT $offset, $records_per_page";
$result_page = $conn_total->query($query_page);

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Transaction History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">';

if ($result_page->num_rows > 0) {
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
    while ($row = $result_page->fetch_assoc()) {
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

    // Display Bootstrap-styled pagination
    echo "<nav aria-label='Page navigation'>
            <ul class='pagination justify-content-center'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<li class='page-item " . ($i == $current_page ? 'active' : '') . "'>
                <a class='page-link' href='?page=$i'>$i</a>
              </li>";
    }
    echo "</ul>
          </nav>";

} else {
    echo "<h2 class='mt-4'>Transaction History</h2>";
    echo "<p class='mt-4'>No transactions found.</p>";
    echo "<a href='index.html' class='btn btn-secondary mt-2'>Home</a>";
}

echo '</body>
</html>';

// Close the database connections
$conn_total->close();
?>
