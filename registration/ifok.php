<?php
$host = "sql105.infinityfree.com";
$user = "if0_39017725";
$pass = "jeZyqYSlUAhhmM"; // Use your InfinityFree account password
$db   = "if0_39017725_parkify_db";


// Connect to DB
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['userName'];
    $password = $_POST['password'];

    // Join user_form and user_passwords to verify password and fetch user_id
    $sql = "SELECT uf.id AS user_id, uf.username, up.password 
            FROM user_form uf 
            JOIN user_passwords up ON uf.id = up.user_id 
            WHERE uf.username = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // ✅ Password is correct — store user_id for later use
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];

            header("Location: ../userboard/ub1.php");
            exit();
        } else {
            echo "<script>
                alert('Incorrect password ❌');
                window.location.href = '../registration/register.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('User not found ❌');
            window.location.href = '../registration/register.php';
        </script>";
    }

    $stmt->close();
}

$conn->close();
?>
