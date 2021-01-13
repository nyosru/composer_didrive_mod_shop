<?php

/**
  определение функций для TWIG
 */
//creatSecret
// $function = new Twig_SimpleFunction('creatSecret', function ( string $text ) {
//    return \Nyos\Nyos::creatSecret($text);
// });
// $twig->addFunction($function);

$function = new Twig_SimpleFunction('shop__get_nav_cats', function ( $db, $cat_id ) {

    $nav_cat = [];

    $cats0 = \Nyos\mod\items::get($db, '020.cats');
    // \f\pa($cats0, 2);

    for ($i = 0; $i <= 10; $i++) {

        if ($i == 0) {
            if (isset($cats0[$cat_id])) {

                $nav_cat[$i] = $cats0[$cat_id];
                $next = $cats0[$cat_id]['a_parentId'];
            } else {
                break;
            }
        } else {

            $e = \f\find_array($cats0, 'a_id', $next);
            // \f\pa($e);

            if ($e === false || !isset($e['a_parentId']))
                break;

            $nav_cat[] = $e;
            $next = $e['a_parentId'];
        }
    }

    krsort($nav_cat);
    return !empty($nav_cat) ? $nav_cat : false;
});
$twig->addFunction($function);


$function = new Twig_SimpleFunction('get_cats_nav', function ( $db, $cat_now = null ) {

    if (empty($cat_now))
        return false;

    if ($cat_now == 'cart')
        return ['cart' => ['id' => 'cart', 'name' => 'Корзина товаров']];

    $cats0 = \Nyos\mod\items::get($db, '020.cats');
    // $cats = cat2cat($cats0);

    $nn = 100;

    $now = [];
    foreach ($cats0 as $k => $v) {
        if (!empty($v['id']) && $v['id'] == $cat_now) {
            $now = $v;
            break;
        }
    }
    // \f\pa($now);
    $cat[$nn] = $now;


    for ($i = 1; $i <= 10; $i++) {

        $nn--;
        $up = $now['a_parentId'] ?? null;

        if (!empty($up)) {
            $now = [];
            foreach ($cats0 as $k => $v) {
                if (!empty($v['a_id']) && $v['a_id'] == $up) {
                    $now = $v;
                    break;
                }
            }
            // \f\pa($now);
            $cat[$nn] = $now;
        }
    }

    ksort($cat);
    // \f\pa($cat);


    return $cat;
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('search_img', function ( $item ) {

    if (empty($item))
        return false;

    if (!empty($item['a_catNumber']))
        return \Nyos\mod\Shop::getImg($item['a_catNumber']);

    return false;
});
$twig->addFunction($function);

/**
 * функция где ищем все входящие каталоги
 * 
 * @param array $cats_ar
 * @param type $now_cat
 * номер пп в базе верхнего каталога
 * @param type $id_cat
 * номер внутренний верхнего каталога
 * @return type
 */
function search_cat_inner(array $cats_ar, $now_cat = null, $id_cat = null) {

//    echo '<br/>' . $now_cat;
//    \f\pa($cats_ar, 2, '', 'cats_in_f');

    $return = [];

    if (empty($id_cat) && !empty($now_cat) && isset($cats_ar[$now_cat])) {
        $id_cat = $cats_ar[$now_cat]['a_id'];
    }

    // \f\pa($id_cat);

    foreach ($cats_ar as $k => $v) {

        if (isset($v['a_parentId']) && $v['a_parentId'] == $id_cat) {

            $return[$v['a_id']] = $v['id'];

            $re = search_cat_inner($cats_ar, null, $v['a_id']);

            if (!empty($re)) {
                $return = array_merge($return, $re);
            }
        }
    }

    return $return;
}

$function = new Twig_SimpleFunction('shop__get_items', function ( $db, $cat = null, $a_id = null, $search = '' ) {

    $cats0 = \Nyos\mod\items::get($db, '020.cats');
    // $cats = cat2cat($cats0);
    // \f\pa($cat, 2, '', 'cat');
    $cat_now = $cats0[$cat] ?? null;
    // \f\pa($cat_now, 2, '', 'cat_now');
    // \f\pa($cats0, 2, '', 'cats0');
    //$nn = 100;
    //$now = [];
//    foreach ($cats0 as $k => $v) {
//        if (!empty($v['id']) && $v['id'] == $cat_now) {
//            $now = $v;
//            break;
//        }
//    }
//      \f\pa($now);

    if (!empty($cat)) {

        $ar_ida_id = search_cat_inner($cats0, $cat);
        // \f\pa($ar_ida_id);

        $sql1 = '';

        if (!empty($ar_ida_id)) {

            $nn = 1;
            foreach ($ar_ida_id as $ida => $id) {

                $sql1 .= (!empty($sql1) ? ' OR ' : '' ) . ' mid.value = :cat' . $nn . ' ';
                \Nyos\mod\items::$var_ar_for_1sql[':cat' . $nn] = $id;

                $nn++;
            }
        }

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'cat_id\' '
                . (!empty($sql1) ? ' AND ( mid.value = :cat OR ' . $sql1 . ' ) ' : ' AND mid.value = :cat ' )
        ;

        \Nyos\mod\items::$var_ar_for_1sql[':cat'] = $cat;
    }

//    if (!empty($id)) {
//        \Nyos\mod\items::$where2 .= ' AND mi.id = :i ';
//        \Nyos\mod\items::$var_ar_for_1sql[':i'] = $id;
//    }

    if (!empty($search)) {

        $s0 = explode(' ', $search);
        if (sizeof($s0) > 1) {

            $ns = 1;

            foreach ($s0 as $kk => $vv) {
                if (!empty($vv)) {
                    \Nyos\mod\items::$where2 .= ' AND mi.head LIKE :ss' . $ns . ' ';
                    // \Nyos\mod\items::$where2 .= ' AND mi.head = :ss ';
                    \Nyos\mod\items::$var_ar_for_1sql[':ss' . $ns] = '%' . $vv . '%';
                    $ns++;
                }
            }
        } else {
            \Nyos\mod\items::$where2 .= ' AND mi.head LIKE :ss ';
            // \Nyos\mod\items::$where2 .= ' AND mi.head = :ss ';
            \Nyos\mod\items::$var_ar_for_1sql[':ss'] = '%' . $search . '%';
        }
    }

    // \Nyos\mod\items::$show_sql = true;
    $items = \Nyos\mod\items::get($db, '021.items');
    // \f\pa($items,2,'','items1');
    // die();
    // ищем по каталожному номеру
    if (empty($items)) {
        // $a_id = '';
        // pse10666
        \Nyos\mod\items::$search['catNumber_search'] = \f\translit($search, 'cifru_bukvu');
        // $items = \Nyos\mod\items::get2($db, '021.items');
        $items = \Nyos\mod\items::get($db, '021.items');
//    echo '<br/>'.__FILE__.' '.__LINE__.' '.$search;
//    echo '<br/>'.__FILE__.' '.__LINE__.' '.$a_id;
        // \f\pa($items);
    }

    if (!empty($a_id)) {

        // echo __LINE__;

        if (!empty($items[$a_id]))
            return $items[$a_id];

        return false;
    } else {
        // echo __LINE__;
        return $items;
    }
});
$twig->addFunction($function);

/**
 * получаем товары что лежат в корзине товаров
 */
$function = new Twig_SimpleFunction('shop__get_items_from_cart', function ( $db ) {

    if (!empty($_SESSION['cart']) && sizeof($_SESSION['cart']) > 0) {

        //\Nyos\mod\items::$show_sql = true;
        \Nyos\mod\items::$search['id'] = array_keys($_SESSION['cart']);
        // $items = \Nyos\mod\items::get($db, '021.items');
        $items = \Nyos\mod\items::get($db, \Nyos\mod\Shop::$mod_items );

        return $items;

    } else {
        return false;
    }
});
$twig->addFunction($function);

/*


$function = new Twig_SimpleFunction('getShopLevel', function () {

    \Nyos\Nyos::getSiteModule();

    //\f\pa($e);
    // \f\pa(\Nyos\Nyos::$all_menu);
    // \f\pa(\Nyos\Nyos::$a_menu);

    foreach (\Nyos\Nyos::$all_menu as $k => $v) {
        if (isset($v['type']) && $v['type'] == 'shop_bu') {
            return $k;
        }
    }

    return false;
});
$twig->addFunction($function);








$function = new Twig_SimpleFunction('shop_bu__searchNavCatalogId', function ( $db, $cat_id ) {
    
    
    $cats = \Nyos\mod\ShopBu::searchNavCatalogId($db,$cat_id);
    // \f\pa($cats);
    
    return $cats;
    return false;
});
$twig->addFunction($function);








$function = new Twig_SimpleFunction('shop_bu__get_items', function ( $db, $get ) {

    // \f\pa($get);
    // \Nyos\mod\items::$get_data_simple = true;
    // $cats = \Nyos\mod\items::getItemsSimple($db, 'catalogs');
    $cats = \Nyos\mod\items::getItemsSimple3($db, 'catalogs');
    // \f\pa($cats,'','','cats');

    $gg = $_GET['ext5'] ?? $_GET['ext4'] ?? $_GET['ext3'] ?? $_GET['ext2'] ?? $_GET['ext1'] ?? 0;

    // \f\pa($gg);
    if ($gg == 0) {


        $tovars = \Nyos\mod\items::getItemsSimple3($db, 'tovars', 'show', 'desc_id');
        //\f\pa($tovars, 2);

        $show_items = [];

        $wer = 0;

        foreach ($tovars as $k => $v) {

            if ($wer >= 30) {
                break;
            }
            
            if( !empty($v['catalog']) ){
            $show_items[] = $v;
            $wer++;
            }
        }
        
    } else {

        $array = null;

        foreach ($cats as $k => $v) {
            if (isset($v['head_translit']) && $v['head_translit'] == $gg) {
                $array = $v;
                break;
            }
        }





        // \f\pa($array,'','','array');
        // \Nyos\mod\items::$get_data_simple = true;
        // \Nyos\mod\items::$show_sql = true;
        // $tovars = \Nyos\mod\items::getItemsSimple($db, 'tovars', 'show', 'desc_id');
        $tovars = \Nyos\mod\items::getItemsSimple3($db, 'tovars', 'show', 'desc_id');
        //\f\pa($tovars, 2);

        $show_items = [];

        $wer = 0;

        foreach ($tovars as $k => $v) {

            // \f\pa($v);
            if ($array === null) {
                if ($wer >= 30) {
                    break;
                }
                $show_items[] = $v;
            }

            if (isset($v['catalog']) && $v['catalog'] == $array['id']) {
                // \f\pa($v);
                $show_items[] = $v;
            }

            $wer++;
        }
    }
    return $show_items;
});
$twig->addFunction($function);








$function = new Twig_SimpleFunction('shop_bu__get_item', function ( $db, $get ) {

    if (!empty($get['ext1']) && is_numeric($get['ext1'])) {
        
    } else {
        return false;
    }

    // \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid1 ON mid1.id_item = mi.id AND mid1.name = \'\' ';
    \Nyos\mod\items::$where2 = ' AND `mi`.`id` = ' . $get['ext1'] . ' ';
    $item = \Nyos\mod\items::getItemsSimple3($db, 'tovars');

    // \f\pa($item);
    return ( $item[$get['ext1']] ?? false );
});
$twig->addFunction($function);
*/