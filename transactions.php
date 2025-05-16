<?php
// transactions.php

require_once 'db.php'; // Reuse connection

// Fetch Transactions
$sql = "SELECT id, ResultCode, ResultDesc, Amount, MpesaReceiptNumber, PhoneNumber, TransactionDate FROM mpesa_transactions ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>STK Push Transactions</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="mb-4 text-center text-primary">M-Pesa STK Push Transactions</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Result Code</th>
                        <th>Result Description</th>
                        <th>Amount</th>
                        <th>Receipt Number</th>
                        <th>Phone Number</th>
                        <th>Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['ResultCode']) ?></td>
                        <td><?= htmlspecialchars($row['ResultDesc']) ?></td>
                        <td><?= htmlspecialchars($row['Amount']) ?></td>
                        <td><?= htmlspecialchars($row['MpesaReceiptNumber']) ?></td>
                        <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                        <td>
                            <?php
                                $rawDate = $row['TransactionDate'];
                                $formattedDate = date('Y-m-d H:i:s', strtotime(substr($rawDate, 0, 8) . ' ' . substr($rawDate, 8)));
                                echo htmlspecialchars($formattedDate);
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No transactions found.</div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS (Optional for future dynamic components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
