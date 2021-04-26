<?php

/**
  класс модуля
 * */

namespace Nyos\mod;

class Shop {

    /**
     * список фоток что лежат на сервере
     * @var масссив
     */
    public static $imgs = null;
    public static $photo_dir = '';
    public static $mod_cats = '';
    public static $mod_items = '';

    public static function getListImg() {

        self::$photo_dir = dir_site_sd . 'photo' . DS;
        self::$imgs = \f\Cash::getVar('list_items_photo');

        if (!empty(self::$imgs)) {
            return;
        }

        $scan = scandir(DR . self::$photo_dir);
        self::$imgs = [];

        foreach ($scan as $v) {
            if (!isset($v{3}))
                continue;

            $img01 = strtolower($v);
            if ($img01 != $v)
                rename(DR . self::$photo_dir . $v, DR . self::$photo_dir . $img01);

            self::$imgs[] = strtolower($v);
        }

        \f\Cash::setVar('list_items_photo', self::$imgs, 3600);
        return;
    }

    /**
     * получаем список итемов
     * @param type $db
     * @param type $cat_id
     * @return array
     */
    public static function getPhotoArticuls($dir = 'import') {

        $photo_dir = DR . dir_site_sd_local . $dir ;
        $tt = \f\File::listFileInDir($photo_dir, 2);

        $photo_articul = [];
        $qq = 1;

        if( !empty($tt) )
        foreach ($tt as $k => $v) {

            $u = pathinfo($v['name']);
            $v['filename'] = $u['filename'];
            $v['file_in_dir'] = str_replace(DR . dir_site_sd_local, '', $v['name']);

            if (file_exists(DR . dir_site_sd_local . $dir . DS . $u['filename'] . '.jpg')) {
                $v['show'] = DS . $dir . DS . $u['filename'] . '.jpg';
            } 
            //
            else{
                if (!$qq <= 50) {
                    copy($v['name'], DR . dir_site_sd_local . $dir . DS . $u['filename'] . '.jpg');
                    $v['show'] = DS . $dir . DS . $u['filename'] . '.jpg';
                    $qq++;
                }
            }
            

        $photo_articul[$u['filename']][] = $v;
        }

        return $photo_articul;
    }

    /**
     * получаем список итемов
     * @param type $db
     * @param type $cat_id
     * @return array
     */
    public static function getItemsNow($db, $cat_id = null, $search = []) {

        $return = [
            'items' => []
        ];

        $sql_v = [];

        $sql2 = '';
        $sql3 = '';
        $nn = 1;

        if (!empty($search)) {

            foreach ($search as $k => $v) {

                $sql2 .= (!empty($sql2) ? 'AND' : '' ) . ' it.art = :s2earch' . $nn . ' OR it.head LIKE :search' . $nn . ' ';
                $sql_v[':s2earch' . $nn] = $v;
                $sql_v[':search' . $nn] = '%' . $v . '%';
                $nn++;
            }

            $sql3 = ' AND ( ' . $sql2 . ' ) ';
        }


        $sql = 'SELECT 

                it.* ,

                CASE
                  WHEN c1.id IS NOT NULL THEN c1.id
                  ELSE NULL
                END cat_id_id,

                CASE
                  WHEN c1.id IS NOT NULL THEN c1.head
                  ELSE NULL
                END cat_name,
                
                    cc1.id cat_up1_id,
                    cc1.head cat_up1,
                    cc2.id cat_up2_id,
                    cc2.head cat_up2,
                    cc3.id cat_up3_id,
                    cc3.head cat_up3,
                    cc4.id cat_up4_id,
                    cc4.head cat_up4,
                    cc5.id cat_up5_id,
                    cc5.head cat_up5
                    ,
                
                CASE
                  WHEN cc1.id IS NOT NULL THEN c1.id
                  WHEN cc2.id IS NOT NULL THEN c2.id
                  WHEN cc3.id IS NOT NULL THEN c3.id
                  WHEN cc4.id IS NOT NULL THEN c4.id
                  WHEN cc5.id IS NOT NULL THEN c5.id
                  ELSE NULL
                END now_cat_id00
                
            FROM    
                ' . \f\db_table(\Nyos\mod\Shop::$mod_items) . ' it
                    
            ' . (!empty($cat_id) ? 'INNER' : 'LEFT' ) . ' JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' c1
                ON ' . (!empty($cat_id) ? 'c1.id = :cat_id' : 'c1.cat_id = it.cat_id' ) . ' AND c1.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' c2
                ON c2.cat_up = c1.cat_id AND c2.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' c3
                ON c3.cat_up = c2.cat_id AND c3.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' c4
                ON c4.cat_up = c3.cat_id AND c4.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' c5
                ON c5.cat_up = c4.cat_id AND c5.status = \'show\'

            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' cc1
                ON cc1.cat_id = it.cat_id AND cc1.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' cc2
                ON cc2.cat_id = cc1.cat_up AND cc2.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' cc3
                ON cc3.cat_id = cc2.cat_up AND cc3.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' cc4
                ON cc4.cat_id = cc3.cat_up AND cc4.status = \'show\'
            LEFT JOIN ' . \f\db_table(\Nyos\mod\Shop::$mod_cats) . ' cc5
                ON cc5.cat_id = cc4.cat_up AND cc5.status = \'show\'
                
            WHERE 

                (
                it.cat_id = c1.cat_id
                OR it.cat_id = c2.cat_id
                OR it.cat_id = c3.cat_id
                OR it.cat_id = c4.cat_id
                OR it.cat_id = c5.cat_id
                )

            ' . (!empty($search) ? $sql3 : '' ) . '

            GROUP BY it.id
            ;';

        $s = $db->prepare($sql);
        
        if (!empty($cat_id))
            $sql_v[':cat_id'] = $cat_id;

        $s->execute($sql_v);
        return $s->fetchAll();
    }

    public static function getImg($img) {

        if (empty(self::$imgs))
            self::getListImg();

        $img1 = strtolower($img);
        $img0 = $img1 . '.jpg';

        if (in_array($img0, self::$imgs))
            return self::$photo_dir . $img0;

        return false;
    }

}