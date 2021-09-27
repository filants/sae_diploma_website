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

if( isset($_GET['addtocart']) ){
	$productID = $_GET['addtocart'];
	$productQuantity = 1;
	
	if( isset($_SESSION['cart'][ $productID ]) ) {
		$_SESSION['cart'][ $productID ] +=1;
	}else{
		$_SESSION['cart'][ $productID ] = $productQuantity;
	}
}

// Selection products from DB

$query_products = "SELECT * FROM product";
$res = mysqli_query($connection, $query_products);
$products = mysqli_fetch_all($res, MYSQLI_ASSOC);
$products_number = 0;

// Login status check

$logged = isset($_SESSION['logged'])  && isset($_SESSION['login_timestamp']) && $_SESSION['logged']==true  && (time() - $_SESSION['login_timestamp']) < 15*60;

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Limmat - Home</title>
</head>
<body>
<div class="content_container">

    <!-- NAVIGATION NAV -->

    <div>
        <a href="index.php"><img class="logo" src="images/logo.png" alt="logo_limmat"></a>
    </div>

    <nav>
        <ul class="navigation">
            <li><a href="index.php" class="nav_active">home</a></li>
            <li><a href="">About us</a></li>
            <li><a href="">Contact</a></li>
            <li><a href="">search</a></li>
            <li id="user_bar"><a href="login.php"><img src="images/icons/konto_icon_transparent.png" alt="Favoriten" id="konto_icon"></a></li>
            <?php if ($logged == true) {  ?>
                <li id="user_logout"><a href="includes/logout.php">Logout</a></li>
            <?php  }  ?>
            <li><a href=""><img src="images/icons/favoriten_icon_transparent.png" alt="Favoriten" id="favoriten_icon"></a></li>
            <li><a href="cart.php"><img src="images/icons/warenkorb_icon_transparent.png" alt="Warenkorb" id="warenkorb_icon"></a></li>
        </ul>
    </nav>

    <!-- BANNER -->

    <div class="banner"><img src="images/visuals/SpringSale.jpg" alt=""></div>

    <!-- PRODUCTS -->

    <section>
        <div class="products_container">
            <?php if(count($products)){ ?>
                <?php foreach($products as $product){ ?>
                    <div class="items">
                        <img src="<?=IMAGEFOLDER?>/product/<?=$product['product_image']?>" alt="limmat_product_<?=$product['product_name']?>">
                        <h4><?php echo $product['product_name'];?></h4>
                        <p class="product_desc"><?=$product['product_desc']?></p>
                        <div class="line"></div>
                        <div class="kauf_container">
                            <a href="?addtocart=<?php echo $product['IDproduct']; ?>" class="kauf_button">kaufen</a>
                            <span class="product_price">CHF <?=$product['product_price']?></span>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
    </section>

</div>

<!-- FOOTER -->

<section class="footer">
	<div class="footer_container">a</div>
</section>

</body>
</html>