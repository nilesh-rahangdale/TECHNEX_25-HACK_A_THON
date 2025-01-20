<?php
// Include the necessary dependencies
require '../vendor/autoload.php'; // Adjust the path according to your directory structure

use PhpOffice\PhpSpreadsheet\IOFactory;

// Database connection
include 'db_connect.php'; 

if (isset($_POST['import'])) {
    // File upload handling
    $file_mimes = array(
        'application/vnd.ms-excel', 
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    if (isset($_FILES['excel_file']['name']) && in_array($_FILES['excel_file']['type'], $file_mimes)) {
        
        $arr_file = explode('.', $_FILES['excel_file']['name']);
        $extension = end($arr_file);

        // Load the Excel file
        $reader = ($extension == 'xls') ? IOFactory::createReader('Xls') : IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($_FILES['excel_file']['tmp_name']);

        // Convert sheet data to an array
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Prepare SQL statements outside of the loop
        $sql_student = "INSERT INTO student_bio (firstname, middlename, lastname, gender, year, course_id, email, connected_to, avatar, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_student = $conn->prepare($sql_student);

        $sql_users = "INSERT INTO users (name, username, password, type, auto_generated_pass, alumnus_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_users = $conn->prepare($sql_users);

        if ($stmt_student === false || $stmt_users === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // Iterate over Excel rows and insert into the database
        // Iterate over Excel rows and insert into the database
foreach ($sheetData as $row) {
    // Skip header row by checking if the row contains specific column names
    if ($row['A'] == 'First Name' && $row['B'] == 'Middle Name' && $row['C'] == 'Last Name') {
        continue; // Skip the header row
    }

    // Extract data from the row
    $firstname = $row['A'] ?? null; // First Name
    $middlename = $row['B'] ?? null; // Middle Name
    $lastname = $row['C'] ?? null; // Last Name
    $gender = $row['D'] ?? null; // Gender
    $year = $row['E'] ?? null; // Batch
    $course_id = $row['F'] ?? null; // Course ID
    $email = $row['G'] ?? null; // Email
    $connected_to = $row['H'] ?? null; // Connected To
    $avatar = $row['I'] ?? 'default_avatar.png'; // Avatar (default if empty)
    $status = $row['J'] ?? 'Not Verified'; // Status (default if empty)

    // Skip rows with missing required fields (adjust according to your specific requirements)
    if (empty($firstname) || empty($lastname) || empty($email)) {
        continue; // Skip the row if any required field is missing
    }

    // Insert into student_bio
    $stmt_student->bind_param("sssssissss", $firstname, $middlename, $lastname, $gender, $year, $course_id, $email, $connected_to, $avatar, $status);
    if (!$stmt_student->execute()) {
        echo "Error executing statement for $firstname: " . $stmt_student->error . "<br>";
        continue; // Skip this row if there's an error
    }

    // Get the newly inserted student ID
    $student_id = $conn->insert_id;

    // Prepare data for users table
    $name = $firstname . ' ' . $middlename . ' ' . $lastname;
    $username = $email;
    $temp_password = bin2hex(random_bytes(4)); // Generates a short random password
    $hashed_password = password_hash($temp_password, PASSWORD_BCRYPT);
    $type = 3; // student user type
    $auto_generated_pass = $temp_password;

    // Insert into users table
    $stmt_users->bind_param("sssssi", $name, $username, $hashed_password, $type, $auto_generated_pass, $student_id);
    if (!$stmt_users->execute()) {
        echo "Error executing statement for $name in users table: " . $stmt_users->error . "<br>";
    }
}


        echo "Data imported successfully!";
    } else {
        echo "Invalid file type. Please upload an Excel file.";
    }
} else {
    echo "No file uploaded or import action not triggered.";
}
?>
