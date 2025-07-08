<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
include 'db.php';

// Get total invoices and data
$totalInvoices = 0;
$chartData = [];

$result = $conn->query("SELECT amount, DATE(created_at) as date FROM invoices ORDER BY created_at ASC");
while ($row = $result->fetch_assoc()) {
    $totalInvoices++;
    $chartData[] = [
        'date' => $row['date'],
        'amount' => (float)$row['amount']
    ];
}

$groupedData = [];
foreach ($chartData as $row) {
    $date = $row['date'];
    if (!isset($groupedData[$date])) $groupedData[$date] = 0;
    $groupedData[$date] += $row['amount'];
}
$labels = array_keys($groupedData);
$amounts = array_values($groupedData);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            flex: 1 1 45%;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .header {
            margin-bottom: 20px;
        }
        #chat-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            z-index: 9999;
        }
        #chat-container {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 420px;
            height: 500px;
            background: white;
            border: 2px solid #007bff;
            border-radius: 12px;
            overflow: hidden;
            z-index: 9998;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Total Invoices: <strong><?php echo $totalInvoices; ?></strong></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
    <div class="grid">
        <div class="card">
            <h3>Monthly Sales</h3>
            <canvas id="monthlySales"></canvas>
        </div>
        <div class="card">
            <h3>Top 5 Sales Product</h3>
            <canvas id="topProducts"></canvas>
        </div>
        <div class="card">
            <h3>Today's vs Yesterday's Sales</h3>
            <canvas id="dayComparison"></canvas>
        </div>
        <div class="card">
            <h3>Sales Comparison</h3>
            <canvas id="salesComparison"></canvas>
        </div>
    </div>
</div>

<!-- Chatbot Floating Button -->
<button id="chat-toggle">🤖</button>

<!-- Chatbot Iframe -->
<div id="chat-container">
  <iframe 
    src="https://fd335077-982d-4243-85f6-7c350b46665d-00-1cpbncacg1ot6.pike.repl.co/" 
    width="100%" 
    height="100%" 
    style="border: none;">
  </iframe>
</div>

<script>
const labels = <?php echo json_encode($labels); ?>;
const amounts = <?php echo json_encode($amounts); ?>;

new Chart(document.getElementById('monthlySales'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Amount',
            data: amounts,
            borderColor: '#007bff',
            fill: true,
            backgroundColor: 'rgba(0,123,255,0.1)',
            tension: 0.3
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('topProducts'), {
    type: 'doughnut',
    data: {
        labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
        datasets: [{
            data: [300, 200, 100, 150, 250],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8']
        }]
    }
});

new Chart(document.getElementById('dayComparison'), {
    type: 'bar',
    data: {
        labels: ['Yesterday', 'Today'],
        datasets: [{
            label: 'Sales',
            data: [500, 700],
            backgroundColor: ['#ffc107', '#007bff']
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('salesComparison'), {
    type: 'bar',
    data: {
        labels: ['This Week', 'Last Week'],
        datasets: [{
            label: 'Sales',
            data: [1200, 900],
            backgroundColor: ['#28a745', '#6c757d']
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

// Chatbot Toggle
const chatToggle = document.getElementById('chat-toggle');
const chatContainer = document.getElementById('chat-container');

chatToggle.addEventListener('click', function () {
    chatContainer.style.display = chatContainer.style.display === 'none' ? 'block' : 'none';
});
</script>
</body>
</html>

