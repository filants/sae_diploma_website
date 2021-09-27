<?php
require_once('../includes/config.php');
require_once('../includes/mysqli-connect.php');
require_once('../includes/functions.php');

session_name(CUSTOM_SESSIONNAME); // Key limmatcms_j210621af
session_start();
session_regenerate_id(); // New ID

$hasError = false; 
$messages = array();

// Input check

if (isset($_POST['email']) && isset($_POST['password'])) {

	$query_email = "SELECT * FROM admin WHERE admin_email = ?";

    // Email validation

    $emailValue = cleanString($_POST['email']);
	
	$prepared = mysqli_prepare($connection, $query_email);
	mysqli_stmt_bind_param($prepared, "s", $emailValue); 
	mysqli_stmt_execute($prepared);
	$res  = mysqli_stmt_get_result($prepared);
	
    // Existing email check

	if (empty($emailValue) || empty(cleanString($_POST['password']))) {
		$hasError = true;
        $messages[] = '<li>Fühlen Sie bitte alle Felder aus</li>';
	} elseif (mysqli_affected_rows($connection) == 0) {
		$hasError = true;
		$messages[] = '<li>Anmeldedaten sind nicht korrekt</li>';
	} else {
		$admin = mysqli_fetch_assoc($res);

        // If ok > create Session

		if(!$hasError && password_verify(cleanString($_POST['password']), $admin['admin_password'])==true ){
			$_SESSION['logged'] = true;
			$_SESSION['login_timestamp'] = time();
			header('Location: productmanager.php');
		
		} else {
			$hasError = true; 
			$messages[] = '<li>Anmeldedaten sind nicht korrekt</li>';
		}
	}
} else {
    $emailValue = "";
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="../images/favicon.png">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <title>Admin - Home</title>
</head>
<body>
<div class="content_container">

    <!-- NAVIGATION NAV -->

    <div>
        <a href="../index.php"><img class="logo" src="../images/logo.png" alt="logo_limmat"></a>
    </div>

    <nav>
        <ul class="navigation">
            <li><a href="../shop.php">Shop</a></li>
            <li><a href="">About us</a></li>
            <li><a href="">Contact</a></li>
            <li id="admin_login"><a href="index.php" class="nav_active" >Admin login</a></li>

        </ul>
    </nav>

    <div class="header_title">
        <img src="../images/titles/admin.png" alt="">
    </div>

    <!-- LOGIN FORM -->

    <form method="POST" action="index.php" class="user_form">
    
        <?php if( count($messages)>0 ){ ?>
            <ul class="alert">
                <?php echo implode('<br>', $messages); ?>
            </ul>
        <?php } ?>

        <label for="email"><h3>Email</h3></label>
        <input type="text" name="email" maxlength="40" value="<?=$emailValue?>">

        <label for="password"><h3>Password</h3></label>
        <input type="password" name="password">

        <button type="submit" name="login" class="primary_button">anmelden</button>
    </form>

</div>

<!-- FOOTER -->

<section class="footer">
	<div class="footer_container">a</div>
</section>

</body>
</html>