<?php


// \f\pa($_SESSION);

if( isset( $_REQUEST['delete_from_cart'] ) ){
    unset($_SESSION['cart'][$_REQUEST['delete_from_cart']]);
    \f\redirect( '/', $_REQUEST['level'].'/cart/');
    die();
}

