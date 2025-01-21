<?php
$mysqli = require __DIR__ . '/admin/db_connect.php';

// Check if the connection is successful
if (!$mysqli instanceof mysqli) {
    die("Database connection failed.");
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

// Prepare the SQL query
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$users = $result->fetch_assoc();

if ($users === null) {
    die("Token not found");
}

if (strtotime($users["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <h1>Reset Password</h1>

    <form method="post" action="process-reset-password.php">

        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New password</label>
        <input type="password" id="password" name="password">

        <label for="password_confirmation">Repeat password</label>
        <input type="password" id="password_confirmation"
               name="password_confirmation">

        <button>Send</button>
    </form>

</body>
</html>
