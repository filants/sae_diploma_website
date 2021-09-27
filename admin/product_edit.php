<?php
require_once('../includes/config.php');
require_once('../includes/mysqli-connect.php');
require_once('../includes/functions.php');
require_once('../includes/sessioncheck.php');

$hasError = false; 
$messages = array();

// Input Check

if(  isset($_POST['product_image']) && isset($_POST['product_name']) && isset($_POST['product_desc']) && isset($_POST['product_price']) ){
	if( isset($_FILES['product_image']) ){
		
		// Move File
		move_uploaded_file($_FILES['product_image']['tmp_name'], IMAGEFOLDERPATH.'/product'.$_FILES['product_image']['name']);
		$_POST['product_image'] = $_FILES['product_image']['name'];		
	
	}
	// Validation

	$product_image = strip_tags( mysqli_real_escape_string($connection, $_POST['product_image']) );
	$product_name = strip_tags( mysqli_real_escape_string($connection, $_POST['product_name']) );
	$product_desc = strip_tags( mysqli_real_escape_string($connection, $_POST['product_desc']) );
	$product_price = strip_tags( mysqli_real_escape_string($connection, $_POST['product_price']) );
	
	// DB involvment
	$query = "INSERT INTO product (product_image, product_name, product_desc, product_price)
	VALUES ('".$product_image."', '".$product_name."', '".$product_desc."', '".$product_price."' ) ";

	

	$res = mysqli_query($connection, $query);
	if( !$res ){
		$hasError = true;
		$messages = '';
		if(SQLDEBUG == true){
			$messages = mysqli_error($connection);
		}
		$errorMsg[] = 'Konnte nicht speichern '.$messages;
	}
	
	// hat geklappt
	if(!$hasError){
		$successMsg = 'Die Daten konnten gespeichert werden';
	}
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
    <title>Admin - Productmanager</title>
</head>
<body>
<div class="content_container">

    <!-- NAVIGATION NAV -->

    <div>
        <a href="../index.php"><img class="logo" src="../images/logo.png" alt="logo_limmat"></a>
    </div>

    <nav>
        <ul class="navigation">
            <li><a href="productmanager.php" class="">product manager</a></li>
            <li><a href="" class="nav_active">hinzugügen</a></li>
            <li id="admin_login"><a href="../includes/logout.php" class="" >Logout</a></li>

        </ul>
    </nav>

    <div class="header_title">
        <img src="../images/titles/admin.png" alt="">
    </div>

    <!-- Form LIST -->

            <form method="POST" action="product_edit.php" class="user_form">

			<?php if ($hasError == true) { ?>
				<div class="alert">
       				 <ul>
		   				 <?php  echo implode('<br>', $errorMsg); ?>
        			</ul>
				</div>
			<?php } ?>

			<?php if (isset($successMsg) ) { ?>
				<div class="alert">
      				<ul>
		  				<?php  echo $successMsg; ?>
       				</ul>
				</div>
			<?php } ?>

                <label for="bild"><h3>Bild</h3></label>
                <input type="file" name="product_image" maxlength="40" value="">
				<input type="hidden" name="product_image" value="">

                <label for="titel"><h3>Titel</h3></label>
                <input type="text" name="product_name" maxlength="40" value="">

				<label for="city"><h3>Beschreibung</h3></label>
                <input type="text" name="product_desc" maxlength="40" value="" rows="5">

                <label for="street"><h3>Price</h3></label>
                <input type="text" name="product_price" maxlength="40" value="">

                <button type="submit" name="register" class="primary_button">hinzufügen</button>
                <a href="productmanager.php" class="secondary_button">zurück</a></button>

            </form>

</div>


<!-- FOOTER -->

<section class="footer">
	<div class="footer_container"></div>
</section>

</body>
</html>