<?php
include 'config.php';

$message = "";

if(isset($_POST['register']))
{
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];

    // STEP 1: check duplicate username/email
    $check = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0)
    {
        $message = "Username or Email already exists!";
    }
    else
    {
        $sql = "INSERT INTO users
        (full_name, username, email, password, birth_date, gender, profile_pic)
        VALUES
        ('$full_name', '$username', '$email', '$password', '$birth_date', '$gender', 'default.jpg')";

        if(mysqli_query($conn, $sql))
        {
            header("Location: login.php");
            exit();
        }
        else
        {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Connectova</title>
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
    <a href="profile.php">Profile</a>
    <a href="chat.php">Chat</a>
</nav>

<h2>Create Account</h2>

<p><?php echo $message; ?></p>

<form method="POST">

    <label>Full Name</label><br>
    <input type="text" name="full_name" required><br><br>

    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <label>Date of Birth</label><br>
    <input type="date" name="birth_date" required><br><br>

    <label>Gender</label><br>
    <select name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select><br><br>

    <button type="submit" name="register">
        Register
    </button>

</form>
<p>
    Already have an account?
    <a href="login.php">Login Here</a>
</p>
</body>
</html>