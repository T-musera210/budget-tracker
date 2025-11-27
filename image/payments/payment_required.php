<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Required</title>
    <style>
        body { text-align: center; padding: 50px; font-family: Arial, sans-serif; }
        .container { background: #fff3cd; padding: 20px; border-radius: 10px; display: inline-block; }
        button { padding: 10px 20px; background: #ff9900; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Required</h2>
        <p>You need to complete the payment to access the Budget Tracker.</p>
        <button onclick="window.location.href='pesapal_payment.php'">Pay Now</button>
    </div>
</body>
</html>
