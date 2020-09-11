<?php

$vv['in_body_end'][] = '<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>';

if (isset($_POST['io']) && isset($_POST['phone']) && isset($_POST['items'])) {

    $msg = 'Новый заказ'
            . PHP_EOL
            . $_REQUEST['io']
            . PHP_EOL
            . $_REQUEST['phone'];

    $summa = 0;

    foreach ($_POST['items'] as $k => $v) {
        $msg .= PHP_EOL . PHP_EOL . $v;

        if (!empty($_POST['price'][$k]) && $_POST['price'][$k] > 0) {
            $msg .= PHP_EOL . $_POST['quantity'][$k] . ' шт. * ' . $_POST['price'][$k] . ' р = ' . ( $_POST['quantity'][$k] * $_POST['price'][$k] ) . ' р';
            $summa += ( $_POST['quantity'][$k] * $_POST['price'][$k] );
        } else {
            $msg .= PHP_EOL . $_POST['quantity'][$k] . ' шт. под заказ';
        }
    }

    $msg .= PHP_EOL . PHP_EOL . 'Итого: ' . number_format($summa, '0', '.', '`') . ' р';
    \nyos\Msg::sendTelegramm($msg, null, 2);
    
    $_SESSION['cart'] = [];
    \f\redirect('/', 'index.php', ['level' => 'show', 'option' => 'cart', 'warn_order' => 'Заказ принят, спасибо']);
}

$vv['tpl_body'] = ( file_exists(dir_site_module_nowlev_tpl . 'body.htm') ? dir_site_module_nowlev_tpl . 'body.htm' : dir_mods_mod_vers_tpl . 'body.htm' );

$vv['in_body_end'][] = '<script src="' . DS . 'vendor' . DS . 'didrive' . DS . 'libs' . DS . 'js' . DS . 'numberformat.js"></script>';
