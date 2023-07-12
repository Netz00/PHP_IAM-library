<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// include your settings which will override default ones
include_once('constants.inc.php');

// load library (don't get confused, this is path inside docker container not inside repo!)
include_once('../IAM-lib/loader.inc.php');

// load your other classes
include_once('class.Db.inc.php');
include_once('class.MyQueries.inc.php');

// Some sort of "ControllerAdvice"
set_exception_handler(function (\Exception $exception) {
    header("Location: " . "/" . "?error=" . $exception->getMessage(),  true,  301);
    die();
});

$myQueries = new MyQueries();

$iam = new IdentityAccessManager(
    $myQueries,
    new Session,
    new Sha256(),
    new AuthCookie($myQueries)
);

$user = $iam->isUserLoggedIn();

if (!empty($_POST)) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $rememberMe = isset($_POST['rememberMe']) ? ($_POST['rememberMe'] == 'on' ? true : false) : false;

    if ($user === null) {
        if ($action == 'register')
            $iam->register($username, $email, $password);
        else if ($action == 'login')
            $user = $iam->login($username, $password, $rememberMe);
        // else if ($action == 'reset')
    } else if ($action == 'logout')
        $iam->logout($user);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>MySQL Example!</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="icon" href="favicon.png">
</head>

<body>

    <!-- Error messages handler -->
    <script>
        function findGetParameter(parameterName) {
            var result = null,
                tmp = [];
            location.search
                .substr(1)
                .split("&")
                .forEach(function(item) {
                    tmp = item.split("=");
                    if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
                });
            return result;
        }
        if (findGetParameter("error"))
            alert(findGetParameter("error"));
    </script>

    <h1>MySQL implementation</h1>
    <div class="section">
        <h2>User space</h2>
        <?php if ($user === null) { ?>
            <div>
                <div class="single_form">
                    <h3>Register</h3>
                    <form action="/" method="post" autocomplete="off">
                        <input type="hidden" name="action" value="register">
                        <input type="text" placeholder="Enter Username" name="username" required>
                        <input type="email" placeholder="Enter Email" name="email" required>
                        <input type="password" placeholder="Enter Password" name="password" required>
                        <input type="submit" value="Submit">
                    </form>
                </div>
                <div class="single_form">
                    <h3>Login</h3>
                    <form action="/" method="post" autocomplete="off">
                        <input type="hidden" name="action" value="login">
                        <input type="text" placeholder="Enter Username" name="username" required>
                        <input type="password" placeholder="Enter Password" name="password" required>
                        <div>
                            <input type="checkbox" id="rememberMe" name="rememberMe">
                            <label for="rememberMe">Remember Me</label>
                        </div>
                        <input type="submit" value="Submit">
                    </form>
                </div>
                <div class="single_form">
                    <h3>Reset password</h3>
                    <form action="/" method="post" autocomplete="off">
                        <input type="hidden" name="action" value="reset">
                        <input type="email" placeholder="Enter Email" name="email" required>
                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
        <?php } else { ?>
            <h3>Welcome</h3>
            <h2> <?php echo $user->username ?></h2>
            <div class="logout">
                <form action="/" method="post" autocomplete="off">
                    <input type="hidden" name="action" value="logout">
                    <input type="submit" value="Logout">
                </form>
            </div>
        <?php } ?>
    </div>


    <div class="section">
        <h2>Storage preview</h2>
        <h3>Users table</h3>
        <?php $allUsers = $myQueries->findAllUsers();
        if ($allUsers) { ?>
            <table>
                <tr>
                    <?php
                    foreach ($allUsers[0] as $prop => $value)
                        echo "<th>$prop</th>";
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach ($allUsers as $id => $props) {
                        echo "<tr>";
                        foreach ($props as $prop => $value)
                            echo "<td>$value</td>";
                    }
                    ?>
                </tr>
            </table>
        <?php } else {
            echo "<p>Table empty</p>";
        } ?>
        <h3>Remember me table</h3>
        <?php $allUsers = $myQueries->findAllRememberMe();
        if ($allUsers) { ?>
            <table>
                <tr>
                    <?php
                    foreach ($allUsers[0] as $prop => $value)
                        echo "<th>$prop</th>";
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach ($allUsers as $id => $props) {
                        echo "<tr>";
                        foreach ($props as $prop => $value)
                            echo "<td>$value</td>";
                    }
                    ?>
                </tr>
            </table>
        <?php } else {
            echo "<p>Table empty</p>";
        } ?>
        <h3>Password reset requests table</h3>
        <?php $allUsers = $myQueries->findAllPwdResetRequests();
        if ($allUsers) { ?>
            <table>
                <tr>
                    <?php
                    foreach ($allUsers[0] as $prop => $value)
                        echo "<th>$prop</th>";
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach ($allUsers as $id => $props) {
                        echo "<tr>";
                        foreach ($props as $prop => $value)
                            echo "<td>$value</td>";
                    }
                    ?>
                </tr>
            </table>

        <?php } else {
            echo "<p>Table empty</p>";
        } ?>
    </div>

</body>

</html>