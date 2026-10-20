<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "birthday_wishnote";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['FirstName'] ?? '';
    $lastName = $_POST['LastName'] ?? '';
    $govPosition = $_POST['GovPosition'] ?? '';
    // Note: Location is intentionally omitted as per requirements/flaw setup

    // INTENTIONAL DATABASE INSERTION FLAW:
    // The query and bind_param completely omit the 'Location' column and variable.
    $sql = "INSERT INTO main_corrupt_politicians (FirstName, LastName, GovPosition) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $firstName, $lastName, $govPosition);
        if ($stmt->execute()) {
            $successMessage = "Success! The wish note has been sent.";
        } else {
            $errorMessage = "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Error preparing statement: " . $conn->error;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kara David's Wish Note</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #1e3a8a; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background-color: #2563eb; color: white; padding: 10px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #1d4ed8; }
        .success { color: green; text-align: center; margin-bottom: 15px; font-weight: bold; }
        .error { color: red; text-align: center; margin-bottom: 15px; font-weight: bold; }
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 10; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background: #fff; padding: 20px; border-radius: 8px; width: 90%; max-width: 300px; text-align: center; }
        .modal-buttons { display: flex; justify-content: space-between; margin-top: 15px; }
        .modal-buttons button { width: 48%; }
        .btn-cancel { background-color: #6b7280; }
        .btn-cancel:hover { background-color: #4b5563; }
    </style>
</head>
<body>

<div class="card">
    <h2>Kara David's Wish Note</h2>
    <?php if(!empty($successMessage)) echo "<div class='success'>$successMessage</div>"; ?>
    <?php if(!empty($errorMessage)) echo "<div class='error'>$errorMessage</div>"; ?>
    
    <form id="wishForm" method="POST" action="">
        <div class="form-group">
            <label for="FirstName">Target's First Name</label>
            <input type="text" id="FirstName" name="FirstName" maxlength="30" required>
        </div>
        <div class="form-group">
            <label for="LastName">Target's Last Name</label>
            <input type="text" id="LastName" name="LastName" maxlength="30" required>
        </div>
        <div class="form-group">
            <label for="GovPosition">Target's Government Position</label>
            <input type="text" id="GovPosition" name="GovPosition" maxlength="20" required>
        </div>
        <div class="form-group">
            <label for="Location">Target's Location</label>
            <input type="text" id="Location" name="Location" maxlength="40">
        </div>
        <button type="submit" id="submitBtn">Send Wish Note</button>
    </form>
</div>

<!-- Modal Overlay -->
<div id="verifyModal" class="modal">
    <div class="modal-content">
        <h3>Verify Target Data</h3>
        <p><strong>First Name:</strong> <span id="mFirstName"></span></p>
        <p><strong>Last Name:</strong> <span id="mLastName"></span></p>
        <p><strong>Position:</strong> <span id="mGovPosition"></span></p>
        <p><strong>Location:</strong> <span id="mLocation"></span></p>
        <div class="modal-buttons">
            <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
            <button type="button" id="confirmBtn">Confirm & Send</button>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('wishForm');
    const modal = document.getElementById('verifyModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('mFirstName').innerText = document.getElementById('FirstName').value;
        document.getElementById('mLastName').innerText = document.getElementById('LastName').value;
        document.getElementById('mGovPosition').innerText = document.getElementById('GovPosition').value;
        document.getElementById('mLocation').innerText = document.getElementById('Location').value;
        modal.style.display = 'flex';
    });

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    confirmBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        form.submit();
    });
</script>

</body>
</html>
