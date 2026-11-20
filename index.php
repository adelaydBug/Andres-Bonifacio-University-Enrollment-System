<?php
session_start();
include('conn.php');

$username = '';
$alert    = '';      // message shown in a popup
$redirect = '';      // page to open after a successful login

if (isset($_POST['submit']))
{
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Prepared statement: blocks SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM usertbl WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $rowval = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);

    // Accepts hashed passwords AND old plain-text ones (so existing accounts keep working)
    $stored = $rowval['password'] ?? '';
    $valid  = $rowval && $stored !== '' &&
              (password_verify($password, $stored) || hash_equals($stored, $password));

    if (!$valid)
    {
        $alert = 'Username or Password is INCORRECT.';
    }
    else
    {
        session_regenerate_id(true);               // new session id after login

        $_SESSION['username'] = $rowval['username'];
        $_SESSION['logcheck'] = 1;

        if ($rowval['user_type'] == 'admin')
        {
            $_SESSION['type'] = 'admin';
            $alert    = 'Successfully logged in as ADMINISTRATOR';
            $redirect = 'Admin/adminindex.php';
        }
        else
        {
            $_SESSION['type']   = 'student';
            $_SESSION['userID'] = $rowval['ID'];
            $alert    = 'Successfully logged in as STUDENT';
            $redirect = 'student/studentindex.php';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>LOGIN PAGE</title>
<link href="style.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="loginForm">

    <!-- Logo (left). Change its size with --logo-size in style.css -->
    <div class="logoBox">
        <img src="logo.jpg?v=<?= filemtime(__DIR__ . '/logo.jpg') ?>" alt="School logo">
    </div>

    <!-- Title + fields + button (right), all centered on the same line -->
    <form method="POST" class="loginPanel" autocomplete="off">
        <h1>System Login</h1>

        <table class="fields">
            <tr>
                <td class="label"><label for="username">Username:</label></td>
                <td>
                    <input type="text" class="text" id="username" name="username"
                           required value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">
                </td>
            </tr>
            <tr>
                <td class="label"><label for="password">Password:</label></td>
                <td>
                    <div class="pwwrap">
                        <input type="password" class="text" id="password" name="password" required>
                        <i id="togglePassword" class="fa-solid fa-eye" title="Show / hide password"></i>
                    </div>
                </td>
            </tr>
        </table>

        <input type="submit" name="submit" value="Login" class="btn">
    </form>

</div>

<script>
// Show / hide password
var toggle   = document.getElementById('togglePassword');
var password = document.getElementById('password');

toggle.addEventListener('click', function () {
    var show = password.type === 'password';
    password.type = show ? 'text' : 'password';
    this.classList.toggle('fa-eye', !show);
    this.classList.toggle('fa-eye-slash', show);
});
</script>

<?php if ($alert): ?>
<script>
alert(<?= json_encode($alert) ?>);
<?php if ($redirect): ?>
window.location = <?= json_encode($redirect) ?>;
<?php endif; ?>
</script>
<?php endif; ?>

</body>
</html>