<?php
session_start();
include "db.php";  // Database connection

// Redirect to login if user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"]; // Get username from session

// Fetch user's budget data
$sql = "SELECT * FROM budget_data WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$budget = $result->fetch_assoc();

// If no budget data exists, insert an empty record
if (!$budget) {
    $sql = "INSERT INTO budget_data (user_id) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $budget = array_fill_keys(['income', 'side_hustle', 'rent', 'utilities', 'transport', 'shopping', 'entertainment', 'maintenance', 'emergency_fund', 'savings'], 0);
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Tracker</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

    <h2>  Budget Tracker </h2>

    <form id="budgetForm">
        <div class="tables">
            <table><caption> Income</caption>
                <tr><th>Category</th><th>Amount (KES)</th></tr>
                <tr><td>Income</td><td><input type="number" name="income" id="income" value="<?php echo $budget['income']; ?>"></td></tr>
                <tr><td>Side Hustle</td><td><input type="number" name="side_hustle" id="sideHustle" value="<?php echo $budget['side_hustle']; ?>"></td></tr>
            </table>

            <table><caption> Needs</caption>
                <tr><th>Category</th><th>Amount (KES)</th></tr>
                <tr><td>Rent</td><td><input type="number" name="rent" id="rent" value="<?php echo $budget['rent']; ?>"></td></tr>
                <tr><td>Utilities</td><td><input type="number" name="utilities" id="utilities" value="<?php echo $budget['utilities']; ?>"></td></tr>
                <tr><td>Transport</td><td><input type="number" name="transport" id="transport" value="<?php echo $budget['transport']; ?>"></td></tr>
                <tr><td>Shopping</td><td><input type="number" name="shopping" id="shopping" value="<?php echo $budget['shopping']; ?>"></td></tr>
            </table>

           

            <table><caption> Wants</caption>
                <tr><th>Category</th><th>Amount (KES)</th></tr>
                <tr><td>Entertainment</td><td><input type="number" name="entertainment" id="entertainment" value="<?php echo $budget['entertainment']; ?>"></td></tr>
                <tr><td>Maintenance</td><td><input type="number" name="maintenance" id="maintenance" value="<?php echo $budget['maintenance']; ?>"></td></tr>
            </table>

            <table><caption> Savings & Investments</caption>
                <tr><th>Category</th><th>Amount (KES)</th></tr>
                <tr><td>Emergency Fund</td><td><input type="number" name="emergency_fund" id="emergencyFund" value="<?php echo $budget['emergency_fund']; ?>"></td></tr>
                <tr><td>Savings</td><td><input type="number" name="savings" id="savings" value="<?php echo $budget['savings']; ?>"></td></tr>
            </table>
        </div>
    </form>

    <div class="chart-amounts-container">
        <div class="chart-container">
            <canvas id="budgetChart"></canvas>
        </div>

        <div id="amounts">
            <p><strong>Needs:</strong> <span id="needsAmount">KES 0</span></p>
            <p><strong>Wants:</strong> <span id="wantsAmount">KES 0</span></p>
            <p><strong>Savings:</strong> <span id="savingsAmount">KES 0</span></p>
            <p><strong>Balance Left:</strong> <span id="balanceAmount">KES 0</span></p>
        </div>
    </div>
</div>

<script>
function saveBudgetData(field, value) {
    fetch("save_budget.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `field=${field}&value=${value}`
    });
}

// Attach event listeners for auto-saving
document.querySelectorAll("input[type='number']").forEach(input => {
    input.addEventListener("input", (event) => {
        let field = event.target.name;
        let value = event.target.value;
        saveBudgetData(field, value);
        updateChart();
    });
});

let ctx = document.getElementById('budgetChart').getContext('2d');
let budgetChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ["Needs", "Wants", "Savings"],
        datasets: [{
            data: [
                <?php echo $budget['rent'] + $budget['utilities'] + $budget['transport'] + $budget['shopping']; ?>,
                <?php echo $budget['entertainment'] + $budget['maintenance']; ?>,
                <?php echo $budget['emergency_fund'] + $budget['savings']; ?>
            ],
            backgroundColor: ["#FF6B6B", "#FFD166", "#06D6A0"]
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

function updateChart() {
    let income = Number(document.getElementById('income').value) + Number(document.getElementById('sideHustle').value);
    let needs = Number(document.getElementById('rent').value) + Number(document.getElementById('utilities').value) +
                Number(document.getElementById('transport').value) + Number(document.getElementById('shopping').value);
    let wants = Number(document.getElementById('entertainment').value) + Number(document.getElementById('maintenance').value);
    let savings = Number(document.getElementById('emergencyFund').value) + Number(document.getElementById('savings').value);

    let balance = income - (needs + wants + savings); // Calculate remaining balance

    budgetChart.data.datasets[0].data = [needs, wants, savings];
    budgetChart.update();

    document.getElementById('needsAmount').innerText = `KES ${needs}`;
    document.getElementById('wantsAmount').innerText = `KES ${wants}`;
    document.getElementById('savingsAmount').innerText = `KES ${savings}`;
    document.getElementById('balanceAmount').innerText = `KES ${balance}`; // Update balance on UI
}

function logout() {
    window.location.href = "logout.php";
}
</script>

<footer>
    <p>&copy; 2025 Budget Tracker. All Rights Reserved.</p>
</footer>
<button onclick="logout()">Logout</button>
</body>
</html>
