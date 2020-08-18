<?php


$vv['in_body_end'][] = '<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>';

if (isset($_POST['io']) && isset($_POST['phone']) && isset($_POST['items'])) {

    $msg = 'Новый заказ';
    $msg .= PHP_EOL
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

    // $vv['warn_order'] = 'Заказ принят, спасибо';
}







/*
  \f\pa($_POST, 2);
  // \f\pa($_FILES,2);

  if (!empty($_POST['add_new_item'])) {

  \f\pa($_POST);

  $dd = $_POST['f'];
  $dd['catalog'] = max($_POST['cat']);

  //    \f\pa($dd);

  $rr = \Nyos\mod\ShopBu::addNewItem($db, $dd, $_FILES['files']);
  //     \f\pa($rr);

  }

  // \Nyos\mod\items::$get_data_simple = true;
  // $items = \Nyos\mod\items::getItemsSimple($db, 'tovars');
  // $items = \Nyos\mod\items::getItemsSimple3($db, 'tovars');
  // \f\pa($items);
 */

$vv['tpl_body'] = ( file_exists(dir_site_module_nowlev_tpl . 'body.htm') ? dir_site_module_nowlev_tpl . 'body.htm' : dir_mods_mod_vers_tpl . 'body.htm' );

//f\pa($vv['now_mod']);
//\f\pa($_POST);
// \f\pa($_GET);
//if (isset($vv['now_mod']['no_cats']{1})) {
//    $vv['tpl_0body'] = \f\like_tpl('sh-no.cat', $vv['dir_module_tpl'], $vv['dir_site_tpl']);
//} else {
// $vv['body'] = \f\like_tpl('sh', $vv['dir_module_tpl'], $vv['dir_site_tpl']);
//}
//echo 
//$vv['tpl_body'] = dir_site_module_nowlev_tpl.'body.htm';

$vv['in_body_end'][] = '<script src="' . DS . 'vendor' . DS . 'didrive' . DS . 'libs' . DS . 'js' . DS . 'numberformat.js"></script>';
