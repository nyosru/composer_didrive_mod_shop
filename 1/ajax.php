<?php

if (isset($get['action']) && $get['action'] == 'scan_new_file') {
    
} else {

    date_default_timezone_set("Asia/Yekaterinburg");
    ob_start('ob_gzhandler');
}

if (1 == 1 || strpos($_SERVER['DOCUMENT_ROOT'], ':')) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    $show_dop = true;
    $status = '';
    $_SESSION['status1'] = true;
}

if (isset($get['action']) && $get['action'] == 'scan_new_file') {
    
} else {

    define('IN_NYOS_PROJECT', TRUE);

//    require( $_SERVER['DOCUMENT_ROOT'] . '/index.session_start.php' );
//    require($_SERVER['DOCUMENT_ROOT'] . '/0.site/0.start.php');
    // define('IN_NYOS_PROJECT', true);

    require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    require( $_SERVER['DOCUMENT_ROOT'] . '/all/ajax.start.php' );
}

if (( isset($get['action']) && $get['action'] == 'scan_new_file' ) || (isset($_GET['action']) && $_GET['action'] == 'scan_new_file')) {

    //f\pa($now);
    // \f\pa($now, 2);
    // $amnu = \Nyos\nyos::get_menu($now['folder']);
    $amnu = \Nyos\nyos::getMenu($vv['folder']);

    //\f\pa($amnu);

    if (isset($amnu) && sizeof($amnu) > 0) {
        foreach ($amnu as $k1 => $v1) {

            //echo '<br/>'.__LINE__.' '.$k1;


            if (isset($v1['type']) && $v1['type'] == 'page.data') {

                echo '<br/>' . __LINE__ . ' ' . $k1;

                if (isset($v1['datain_name_file']) && file_exists($_SERVER['DOCUMENT_ROOT'] . DS . '9.site' . DS . $now['folder'] . DS . 'download' . DS . 'datain' . DS . $v1['datain_name_file'])) {

//                    f\pa($v1);
//                    f\pa($amnu[$_GET['level']] );
//                    die();

                    require_once './../class.php';

                    Nyos\mod\PageData::parseFile(
                            $_SERVER['DOCUMENT_ROOT'] . DS . '9.site' . DS . $now['folder'] . DS . 'download' . DS . 'datain' . DS . $v1['datain_name_file'], $now['folder'], $v1['cfg.level'], ( isset($v1['type_file_data']) ? $v1['type_file_data'] : null)
                    );

                    echo '<br/>обработка файла данных прошла успешно';
                } else {
                    echo '<br/>файл данных не обнаружен';
                }
            }
        }
    }

    if (isset($get['action']) && $get['action'] == 'scan_new_file') {
        
    } else {
        die('Спасибо');
    }
}

// если норм работа
else {




    if (isset($_REQUEST['id']{0}) && isset($_REQUEST['s']{5}) &&
            Nyos\nyos::checkSecret($_REQUEST['s'], $_REQUEST['id']) === true) {
        
    } else {
        f\end2('Произошла неописуемая ситуация #' . __LINE__ . ' обратитесь к администратору', 'error', array('line' => __LINE__));
    }


    
    
    //
    if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'remove_from_cart') {

        if (isset($_SESSION['cart'][$_REQUEST['id']])) {

            $e = $_SESSION['cart'];
            unset($e[$_REQUEST['id']]);
            $_SESSION['cart'] = $e;

            $_SESSION['kolvo'] = sizeof($_SESSION['cart']);
        }

        \f\end2('окей, удалили');
    } 
    
    //
    elseif (!empty($_REQUEST['action']) && ( $_REQUEST['action'] == 'shop__item_add' || $_REQUEST['action'] == 'shop__item_remove' )) {

        if (isset($_SESSION['cart'][$_REQUEST['id']]['kolvo'])) {

            if ($_REQUEST['action'] == 'shop__item_add') {
                $_SESSION['cart'][$_REQUEST['id']]['kolvo'] ++;
            } elseif ($_REQUEST['action'] == 'shop__item_remove') {

                if ($_SESSION['cart'][$_REQUEST['id']]['kolvo'] > 0)
                    $_SESSION['cart'][$_REQUEST['id']]['kolvo'] --;
            }
        }

        \f\end2('окей', true, ['new_kolvo' => $_SESSION['cart'][$_REQUEST['id']]['kolvo']]);
        
    } 
    
    //
    elseif (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'add_item_to_cart') {

        $_SESSION['cart'][$_REQUEST['aj_id']] = [];

        foreach ($_REQUEST as $k => $v) {
            if (strpos($k, 'aj_') !== false) {
                $_SESSION['cart'][$_REQUEST['aj_id']][str_replace('aj_', '', $k)] = $v;
            }
        }

        \f\end2('добавлено', true,
                [
                    'kolvo' => sizeof($_SESSION['cart'])
                    ,
                    'to_cart' => $_SESSION['cart']
                ]
        );
    }
    
    if (isset($_GET['level']{0})) {

        require_once ( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'sql.start.php' );

        $amnu = Nyos\nyos::get_menu($now['folder']);

        // f\pa($now);
        //f\pa($amnu);

        if (isset($amnu[$_GET['level']]['type']) && $amnu[$_GET['level']]['type'] == 'page.data') {

            // f\pa($amnu[$_GET['level']] );
        }

        f\end2('ok', true, array('line' => __LINE__));
    }

    f\end2('Произошла неописуемая ситуация #' . __LINE__ . ' обратитесь к администратору', 'error', array('line' => __LINE__));
}