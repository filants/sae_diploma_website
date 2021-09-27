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

// Cart

if( !isset($_SESSION['cart'] ) ){
	$_SESSION['cart'] = array();
}
// Delete from the cart

if( isset($_GET['delete']) ){
	$deleteID = $_GET['delete']; // ID to be deleted
	if( isset($_SESSION['cart'][$deleteID]) ){
		unset($_SESSION['cart'][$deleteID]);
	} 
}

// Selection products from DB

$cart_products = array();

foreach( $_SESSION['cart'] as $productID => $quantity ){
	$query = "SELECT * FROM product WHERE IDproduct = ?";
	
	$prepared = mysqli_prepare($connection, $query);
	mysqli_stmt_bind_param($prepared, "s", $productID); 
	mysqli_stmt_execute($prepared);
	$res  = mysqli_stmt_get_result($prepared);
	
	if( mysqli_affected_rows($connection) > 0 ){
		$product = mysqli_fetch_assoc($res);
		$product['quantity'] = $quantity; 
		$cart_products[] = $product;
	}
}

// login status check

$logged = isset($_SESSION['logged']) && isset($_SESSION['userid']) && isset($_SESSION['login_timestamp'])
&& $_SESSION['logged']==true  && (time() - $_SESSION['login_timestamp']) < 15*60;

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Limmat - Warenkorb</title>
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
			<?php if ($logged == true) {  ?>
                <li id="user_logout"><a href="includes/logout.php">Logout</a></li>
            <?php  }  ?>
            <li><a href=""><img src="images/icons/favoriten_icon_transparent.png" alt="Favoriten" id="favoriten_icon"></a></li>
            <li><a href="cart.php"><img src="images/icons/warenkorb_icon_transparent.png" alt="Warenkorb" id="warenkorb_icon_active"></a></li>
        </ul>
    </nav>

    <!-- CART -->

    <div class="header_title">
		<img class="titles" src="images/titles/cart.png" alt="cart">
	</div>

    <section>
			<form method="POST" action="cart.php" class="cart_form">

                    <!-- IF cart is empty --> 

					<?php if( count($cart_products) == 0 ){
						    echo 'Warenkorb ist leer';
					      }else{
						    $total = 0; 
						    foreach($cart_products as $product){
							
							// Quantity / Price calculation

							$row_total = $product['product_price'] * $product['quantity']; 
							$total += $row_total; 
					?>

                    <!-- PRODUCTS COLUMN -->

                    <div class="cart_items">
						<img class="cart_img" src="<?=IMAGEFOLDER?>/product/<?=$product['product_image']?>" alt="limmat_product_<?=$product['product_name']?>">
						<div class="cart_desc">
							<h4><?=$product['product_name']?></h4>
                        	<p class="product_desc"> <?=$product['product_desc']?> <span class="each_price">CHF <?=$product['product_price']?> </span></p>
                       		<div class="line"></div>
                        	<div class="price_container">
								<span class="cart_qnt">Menge: <?=$product['quantity']?> </span><br>
								<span class="cart_product_price">CHF <?=$row_total?> </span><br>
								<a href="cart.php?delete=<?=$product['IDproduct']?>" class="delete_button">entfernen</a>
							</div>
						</div>
                    </div>
						<?php } ?>

					<div class="total_price_container">

						<div class="line"></div>

						<p class="product_desc">Nettobetrag <span class="each_price">CHF <?=number_format($total-($total*.077), 2)?></span> </p>
						<p class="product_desc">MwSt <span class="each_price">CHF <?=number_format($total*.077, 2)?></span> </p>
						<p class="product_desc">Gesamtsumme <span class="total_price">CHF <?=number_format($total, 2)?></span></p>

					</div>
					
					<div class="cart_buttons">
						<a href="login.php" class="primary_button">kaufen</a>
               			<a href="index.php" class="back_button">zurück</a>
					</div>

					<?php } ?>
					
			</form>
             
    </section>

</div>

<!-- FOOTER -->

<section class="footer">
	<div class="footer_container"></div>
</section>

</body>
</html>