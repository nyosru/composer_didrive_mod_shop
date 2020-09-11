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

    $sql = 'SELECT '
            . ' c1.a_id cat1, '
            . ' c1.head cat1_head, '
            . ' c2.a_id cat2, '
            . ' c2.head cat2_head, '
            . ' c3.a_id cat3, '
            . ' c3.head cat3_head, '
            . ' c4.a_id cat4 , '
            . ' c4.head cat4_head , '
            . ' c5.a_id cat5 , '
            . ' c5.head cat5_head '
            // . ', c6.a_id cat6 '
            . ' FROM mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c1 '
            . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c2 ON c2.a_id = c1.a_parentid '
            . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c3 ON c3.a_id = c2.a_parentid '
            . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c4 ON c4.a_id = c3.a_parentid '
            . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c5 ON c5.a_id = c4.a_parentid '
            // . ' LEFT JOIN mod_'.\f\translit( \Nyos\mod\parsing_xml1c::$mod_cats , 'uri2').' c6 ON c6.a_parentid = c5.a_id  '
            . ' WHERE c1.a_id = :cat LIMIT 1 ; ';

    $ff = $db->prepare($sql);
    $ff->execute([':cat' => $cat_id]);
    return $ff->fetch();
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('shop__get_nav_cats_down', function ( $db, $cat_id ) {

    $sql = 'SELECT '
            . ' c1.a_id cat1, '
            . ' c1.head cat1_head, '
            . ' c2.a_id cat2, '
            . ' c2.head cat2_head '
//            . ' , '
//            . ' c1r.a_id cat1r, '
//            . ' c1r.head cat1r_head, '
//            . ' c2r.a_id cat2r , '
//            . ' c2r.head cat2r_head '
//            . ' c5.a_id cat5 , '
//            . ' c5.head cat5_head '
            // . ', c6.a_id cat6 '
            . ' FROM mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c1 '
            . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c2 ON c1.a_id = c2.a_parentid '

//            . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c1r ON c1r.a_parentid = c1.a_parentid '
//            . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c2r ON c1r.a_id = c2r.a_parentid '
//            
            // . ' LEFT JOIN mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2') . ' c5 ON c5.a_id = c4.a_parentid '
            // . ' LEFT JOIN mod_'.\f\translit( \Nyos\mod\parsing_xml1c::$mod_cats , 'uri2').' c6 ON c6.a_parentid = c5.a_id  '
            . ' WHERE c1.a_id = :cat ; ';

    $ff = $db->prepare($sql);
    $ff->execute([':cat' => $cat_id]);

    $re = [];

    while ($r = $ff->fetch()) {

        $re['head'] = $r['cat1_head'];

        if (!empty($r['cat2']))
            $re['in'][$r['cat2']] = $r['cat2_head'];
    }

    return $re;
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
            $cat[$nn] = $now;
        }
    }

    ksort($cat);
    return $cat;
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('search_img', function ( $item ) {

    if (empty($item))
        return false;

    if (file_exists(DR . DS . 'sites' . DS . \Nyos\Nyos::$folder_now . DS . 'download' . DS . 'photo' . DS . strtolower($item) . '.jpg')) {
        return '/sites/' . \Nyos\Nyos::$folder_now . '/download' . DS . 'photo' . DS . strtolower($item) . '.jpg';
    } elseif (file_exists(DR . DS . 'sites' . DS . \Nyos\Nyos::$folder_now . DS . 'download' . DS . 'photo' . DS . strtolower($item))) {
        return '/sites/' . \Nyos\Nyos::$folder_now . '/download' . DS . 'photo' . DS . strtolower($item);
    } elseif (file_exists(DR . DS . 'sites' . DS . \Nyos\Nyos::$folder_now . DS . 'download' . DS . 'photo' . DS . $item)) {
        return '/sites/' . \Nyos\Nyos::$folder_now . '/download' . DS . 'photo' . DS . $item;
    }

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

    $return = [];

    if (empty($id_cat) && !empty($now_cat) && isset($cats_ar[$now_cat])) {
        $id_cat = $cats_ar[$now_cat]['a_id'];
    }

    foreach ($cats_ar as $k => $v) {

        if (isset($v['a_parentid']) && $v['a_parentid'] == $id_cat) {

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

    // \f\pa( [ $cat , $a_id , $search ] );

    if (!empty($cat)) {

        $mod_db_cat = 'mod_' . \f\translit(\Nyos\mod\parsing_xml1c::$mod_cats, 'uri2');

        $sql = 'SELECT c1.a_id cat1, c2.a_id cat2, c3.a_id cat3, c4.a_id cat4 , c5.a_id cat5 '
                // . ', c6.a_id cat6 '
                . ' FROM ' . $mod_db_cat . ' c1 '
                . PHP_EOL
                . ' LEFT JOIN ' . $mod_db_cat . ' c2 ON c1.a_id = c2.a_parentid '
                . PHP_EOL
                . ' LEFT JOIN ' . $mod_db_cat . ' c3 ON c3.a_parentid = c2.a_id  '
                . PHP_EOL
                . ' LEFT JOIN ' . $mod_db_cat . ' c4 ON c4.a_parentid = c3.a_id  '
                . PHP_EOL
                . ' LEFT JOIN ' . $mod_db_cat . ' c5 ON c5.a_parentid = c4.a_id  '
//                 . ' LEFT JOIN mod_'.\f\translit( \Nyos\mod\parsing_xml1c::$mod_cats , 'uri2').' c6 ON c6.a_parentid = c5.a_id  '
                . PHP_EOL
                . ' WHERE c1.a_id = :cat ';
        // \f\pa($sql);
        // echo '<pre>'.$sql.'</pre>';

        $ff = $db->prepare($sql);
        $ff->execute([':cat' => $cat]);
        $cats0 = [];
        while ($r = $ff->fetch()) {
            for ($e = 1; $e <= 5; $e++) {
                if (empty($cats0[$r['cat' . $e]]) && !empty($r['cat' . $e])) {
                    $cats0[$r['cat' . $e]] = 1;
                    \Nyos\mod\items::$search['a_categoryid'][] = $r['cat' . $e];
                }
            }
        }

        // \f\pa(\Nyos\mod\items::$search['a_categoryid']);
    }

    if (!empty($search)) {

        $s0 = explode(' ', $search);
        if (sizeof($s0) > 1) {
            foreach ($s0 as $kk => $vv) {
                if (!empty($vv)) {
                    \Nyos\mod\items::$liked_and['head'][] = $vv;
                }
            }
        } else {
            \Nyos\mod\items::$liked_and['head'][] = $search;
        }
    }

    if (empty($_REQUEST['search']) && !isset($_REQUEST['option']) || ( isset($_REQUEST['option']) && $_REQUEST['option'] == 'index' ))
        \Nyos\mod\items::$sql_limit = 40;

    // \Nyos\mod\items::$show_sql = true;
    $items = \Nyos\mod\items::get($db, '021.items');
    // ищем по каталожному номеру
    if (empty($items)) {
        \Nyos\mod\items::$search['catnumber_search'] = \f\translit($search, 'uri3');
        $items = \Nyos\mod\items::get($db, '021.items');
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
        $items = \Nyos\mod\items::get($db, '021.items');

        return $items;

        $cats0 = \Nyos\mod\items::get($db, '020.cats');
        $cat_now = $cats0[$cat] ?? null;

        if (!empty($cat)) {

            $ar_ida_id = search_cat_inner($cats0, $cat);
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

        if (!empty($search)) {

            $s0 = explode(' ', $search);
            if (sizeof($s0) > 1) {

                $ns = 1;

                foreach ($s0 as $kk => $vv) {
                    if (!empty($vv)) {
                        \Nyos\mod\items::$where2 .= ' AND mi.head LIKE :ss' . $ns . ' ';
                        \Nyos\mod\items::$var_ar_for_1sql[':ss' . $ns] = '%' . $vv . '%';
                        $ns++;
                    }
                }
            } else {
                \Nyos\mod\items::$where2 .= ' AND mi.head LIKE :ss ';
                \Nyos\mod\items::$var_ar_for_1sql[':ss'] = '%' . $search . '%';
            }
        }

        // \Nyos\mod\items::$show_sql = true;
        $items = \Nyos\mod\items::get($db, '021.items');
        // ищем по каталожному номеру
        if (empty($items)) {
            \Nyos\mod\items::$search['catNumber_search'] = \f\translit($search, 'cifru_bukvu');
            $items = \Nyos\mod\items::get2($db, '021.items');
        }

        if (!empty($a_id)) {

            if (!empty($items[$a_id]))
                return $items[$a_id];

            return false;
        } else {
            return $items;
        }
    } else {
        return false;
    }
});
$twig->addFunction($function);
