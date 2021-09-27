<?php
require_once('includes/config.php');
require_once('includes/mysqli-connect.php');
require_once('includes/functions.php');

// Session start

session_name(CUSTOM_SESSIONNAME); // Key limmatcms_j210621af
session_start();
session_regenerate_id(); // New ID

// Error logs

$hasError = false;
$messages = array();

// Input check

    if(    isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['birth'])
        && isset($_POST['street']) && isset($_POST['postcode']) && isset($_POST['city']) && isset($_POST['country'])
        && isset($_POST['email']) && isset($_POST['password']) ){


        $query_user = "INSERT INTO user (user_title, user_firstname, user_lastname, user_birth, user_email, user_password)
        VALUES (?,?,?,?,?,?)";

        // Empty fields check

        if(   !isset($_POST['title']) || empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['birth'])
            || empty($_POST['street']) || empty($_POST['postcode']) || empty($_POST['city']) || empty($_POST['country'])
            || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password2']) || !isset($_POST['agreement'])){

                $hasError = true;
                $messages[] = '<li>Fühlen Sie bitte alle Felder aus</li>';
        }

        // Firstname validation

        $firstnameValue = cleanString($_POST['firstname']);

        if (empty($firstnameValue)) {
            $hasError = true;
        } elseif (strlen($firstnameValue) >= 40) {
            $hasError = true;
            $messages[] = '<li>Vorname darf nicht mehr als 40 Buchstaben enthalten</li>';
        } elseif (strlen($firstnameValue) < 2) {
            $hasError = true;
            $messages[] = '<li>Vorname darf nicht weniger als 2 Buchstaben enthalten</li>';
        }

        // Lastname validation

        $lastnameValue = cleanString($_POST['lastname']);

        if (empty($lastnameValue)) {
            $hasError = true;
        } elseif (strlen($lastnameValue) >= 40) {
            $hasError = true;
            $messages[] = '<li>Nachname darf nicht mehr als 40 Buchstaben enthalten</li>';
        } elseif (strlen($lastnameValue) < 2) {
            $hasError = true;
            $messages[] = '<li>Nachname darf nicht weniger als 2 Buchstaben enthalten</li>';
        }

        // Birth validation

        $birthValue = cleanString($_POST['birth']);
        $birthRegex = '/^([0-2][0-9]|(3)[0-1])(\.)(((0)[0-9])|((1)[0-2]))(\.)\d{4}$/'; // Data format dd.mm.yyyy

        if (empty($birthValue)){
            $hasError = true;
        } elseif (!preg_match($birthRegex, $birthValue)) {
            $hasError = true;
            $messages[] = '<li>Geburtsdatum ist ungültig</li>';
            $birthValue = null;
        }

        // Adress validation

        $streetValue = cleanString($_POST['street']);

        if (empty($streetValue)) {
            $hasError = true;
        } elseif (strlen($streetValue) >= 50) {
            $hasError = true;
            $messages[] = '<li>Adresse darf nicht mehr als 50 Buchstaben enthalten</li>';
        } elseif (strlen($streetValue) < 2) {
            $hasError = true;
            $messages[] = '<li>Adresse darf nicht weniger als 2 Buchstaben enthalten</li>';
        }

        // Postcode validation

        $postcodeValue = cleanString($_POST['postcode']);
        $postcodeRegex = '/^[0-9]*$/'; // dd.mm.yyyy

        if (strlen($postcodeValue) > 5 || strlen($postcodeValue) < 4 || !preg_match($postcodeRegex, $postcodeValue)) {
            $hasError = true;
            $messages[] = '<li>PLZ muss zwischen 4 und 5 Zahlen enthalten</li>';
        }

        // City validation

        $cityValue = cleanString($_POST['city']);

        if (empty($cityValue)) {
            $hasError = true;
        } elseif (strlen($cityValue) >= 40) {
            $hasError = true;
            $messages[] = '<li>Stadt darf nicht mehr als 40 Buchstaben enthalten</li>';
        } elseif (strlen($cityValue) < 2) {
            $hasError = true;
            $messages[] = '<li>Stadt darf nicht weniger als 2 Buchstaben enthalten</li>';
        }

        // Country validation

        $countryValue = cleanString($_POST['country']);

        // Email validation

        $emailValue = cleanString($_POST['email']);

        $query = "SELECT * FROM user WHERE user_email = ?";
	
	    $prepared = mysqli_prepare($connection, $query);
	    mysqli_stmt_bind_param($prepared, "s", $emailValue); 
	    mysqli_stmt_execute($prepared);
	    $res  = mysqli_stmt_get_result($prepared);

        if (empty($emailValue)) {
            $hasError = true;
        } elseif (mysqli_affected_rows($connection) == 1) { // Existing Email validation
    		$hasError = true;
    		$messages[] = '<li>E-Mail Adresse existiert bereits</li>';
        } elseif (!filter_var($emailValue, FILTER_VALIDATE_EMAIL) || strlen($emailValue) >= 40) {
            $hasError = true;
            $messages[] = '<li>E-Mail Adresse ist ungültig</li>';
        }

        // Password validation 

        $passwordValue = cleanString($_POST['password']);
        $passwordRegex = '/(?=(.*[0-9]))((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.{8,}$/'; // 1 lowercase letter, 1 uppercase letter, 1 number, and be at least 8 characters long
        
        if (empty($passwordValue)) {
            $hasError = true;
        } elseif (!preg_match($passwordRegex, $passwordValue)) {
            $hasError = true;
            $messages[] = '<li>Passwort muss mindestens 8 Zeichnen enhtalzen und eine Kombination aus Groß-, Kleinbuchstaben und Ziffern sein</li>';
        } elseif ( $passwordValue !== $_POST['password2'] ){ // If passwords matches
            $hasError = true;
            $messages[] = '<li>Die Eingabe der Passworte stimmt nicht überein. Bitte noch einmal versuchen</li>';
        }

        // If all ok > Data send

        if( $hasError == false ){
            $password_hash = password_hash($passwordValue, PASSWORD_DEFAULT); // password hash
    
            $prepared = mysqli_prepare($connection, $query_user);
            mysqli_stmt_bind_param($prepared, "ssssss", cleanString($_POST['title']), $firstnameValue, $lastnameValue, $birthValue, $emailValue, $password_hash); 
            mysqli_stmt_execute($prepared);
            $userID = mysqli_insert_id($connection);
        
            echo 'das ist die neue ID des Kundendatensatzes: '.$userID; 
            
            // Adding adress to the user ID

            $query_adress = "INSERT INTO adress (adress_street, adress_postcode, adress_city, adress_country, user_IDuser)
            VALUES (?,?,?,?,?)";
    
            $prepared2 = mysqli_prepare($connection, $query_adress);
            mysqli_stmt_bind_param($prepared2, "ssssi", $streetValue, $postcodeValue, $cityValue, $countryValue, $userID);
            mysqli_stmt_execute($prepared2);

            // Create session

            $_SESSION['logged'] = true;
			$_SESSION['login_timestamp'] = time();

            // Redirect to the Shop
            
            header('Location: index.php');
        }
        
     // Keep form values

     } else {
        $firstnameValue = "";
        $lastnameValue = "";
        $streetValue = "";
        $postcodeValue = "";
        $cityValue = "";
        $emailValue = "";
        $birthValue = "";
     }

// Login status check

$logged = isset($_SESSION['logged'])  && isset($_SESSION['login_timestamp']) && $_SESSION['logged']==true  && (time() - $_SESSION['login_timestamp']) < 15*60;

// Redirect if already logged

if ($logged == true) {
    header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Limmat - Registration</title>
</head>
<body>
<div class="content_container">

    <!-- NAVIGATION NAV -->

    <div>
        <a href="index.php"><img class="logo" src="images/logo.png" alt="logo_limmat"></a>
    </div>

    <nav>
        <ul class="navigation">
            <li><a href="index.php">home</a></li>
            <li><a href="">About us</a></li>
            <li><a href="">Contact</a></li>
            <li><a href="">search</a></li>
            <li id="user_bar"><a href="login.php"><img src="images/icons/konto_icon_transparent.png" alt="Favoriten" id="konto_icon"></a></li>
            <li><a href=""><img src="images/icons/favoriten_icon_transparent.png" alt="Favoriten" id="favoriten_icon"></a></li>
            <li><a href="cart.php"><img src="images/icons/warenkorb_icon_transparent.png" alt="Warenkorb" id="warenkorb_icon"></a></li>
        </ul>
    </nav>

    <!-- REGISTRATION FORM -->

        <div class="header_title">
            <img src="images/titles/register.png" alt="register">
        </div>

            <form method="POST" action="registration.php" class="user_form">

                <?php if( count($messages)>0 ){ ?>
                    <ul class="alert">
                        <?php echo implode('<br>', $messages); ?>
                    </ul>
                <?php } ?>

                <label><h3>Titel</h3>
                    <label for="mr">
                        <input class="titel_herr" type="radio" name="title" value="herr" <?php if (isset($_POST['title']) && cleanString($_POST['title']) == "herr") echo "checked";?>>
                        Herr
                    </label>

                    <label for="mrs">
                        <input type="radio" name="title" value="frau" <?php if (isset($_POST['title']) && cleanString($_POST['title']) == "frau") echo "checked";?>>
                        Frau
                    </label>
                </label>

                <label for="firstname"><h3>Vorname</h3></label>
                <input type="text" name="firstname" maxlength="40" value="<?=$firstnameValue?>">

                <label for="lastname"><h3>Nachname</h3></label>
                <input type="text" name="lastname" maxlength="40" value="<?=$lastnameValue?>">

                <label for="birth"><h3>Geburtsdatum</h3></label>
                <input type="text" name="birth" placeholder="dd.mm.yyyy" maxlength="10" value="<?=$birthValue?>">

                <label for="street"><h3>Adresse</h3></label>
                <input type="text" name="street" maxlength="50" value="<?=$streetValue?>">

                <label for="postcode"><h3>PLZ</h3></label>
                <input type="text" name="postcode" maxlength="5" value="<?=$postcodeValue?>">

                <label for="city"><h3>Stadt</h3></label>
                <input type="text" name="city" maxlength="40" value="<?=$cityValue?>">

                <label for="country"><h3>Land</h3></label>
                <select name="country">
                    <option value="" selected></option>
                    <option value="ch" <?php if (isset($countryValue) && $countryValue == "ch") echo "selected";?>>Schweiz</option>
                    <option value="de" <?php if (isset($countryValue) && $countryValue == "de") echo "selected";?>>Deutschland</option>
                    <option value="au" <?php if (isset($countryValue) && $countryValue == "au") echo "selected";?>>Österreich</option>
                </select>

                <label for="email"><h3>Email</h3></label>
                <input type="text" name="email" maxlength="40" value="<?=$emailValue?>">

                <label for="password"><h3>Passwort</h3></label>
                <input type="password" name="password">

                <label or="password2"><h3>Passwort erneut eingeben</h3></label>
                <input type="password" name="password2">

                <label class="conditions_box"><input type="checkbox" name="agreement" value="yes" <?php if (isset($_POST['agreement']) && cleanString($_POST['agreement']) == "yes") echo "checked";?>> Ja, ich akzeptiere die AGB</label>

                <button type="submit" name="register" class="primary_button">registrieren</button>
                <a href="login.php" class="secondary_button">anmelden</a></button>

            </form>
</div>

<!-- FOOTER -->

<section class="footer">
	<div class="footer_container">a</div>
</section>

</body>
</html>