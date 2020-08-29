<?php

/**
  класс модуля
 * */

namespace Nyos\mod;

//if (!defined('IN_NYOS_PROJECT'))
//    throw new \Exception('Сработала защита от розовых хакеров, обратитесь к администрратору');

class Shop {

    /**
     * список фоток что лежат на сервере
     * @var масссив
     */
    public static $imgs = null;
    public static $photo_dir = '';

    public static function getListImg() {

        \f\timer_start(1);

        // \f\pa(dir_site_sd);

        self::$photo_dir = dir_site_sd . 'photo' . DS;
        
        self::$imgs = \f\Cash::getVar('list_items_photo');

        if (!empty(self::$imgs)) {
            // \f\pa(\f\timer_stop(1));
            return ;
        }

        $scan = scandir(DR . self::$photo_dir);

        self::$imgs = [];

        foreach ($scan as $v) {

            if ( empty($v) || $v == '.' || $v == '..' )
                continue;

            $img01 = strtolower($v);

            if ($img01 != $v)
                rename(DR . self::$photo_dir . $v, DR . self::$photo_dir . $img01);

            self::$imgs[] = strtolower($v);
        }

        \f\Cash::setVar('list_items_photo', self::$imgs, 3600);
        return;
        // \f\pa(\f\timer_stop(1));

    }

    public static function getImg($img) {

        if (empty(self::$imgs))
            self::getListImg();

        $img1 = strtolower($img);

        $img0 = $img1 . '.jpg';

        if (in_array($img0, self::$imgs)) {
            // \f\pa(self::$photo_dir . $img0);
            return self::$photo_dir . $img0;
        }

        return false;
    }

}
