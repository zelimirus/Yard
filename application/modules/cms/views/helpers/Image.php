<?php

/**
 * Returns image src(apsoluth path) of one of image servers.
 * 
 * @author Vladimir Kanazir <canny@funtelecom.rs>
 * @author Aleksandar Varnicic <aleksandar.varnicic@golive.rs>
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * 
 * @param $path - posible path params : albums_path, artists_path, genres_path, banners_path
 * @param $img - image filename
 */
class Zend_View_Helper_Image extends Zend_View_Helper_Abstract
{

    protected $counter = 0;

    public function image($path, $img)
    {
        $servers = preg_split('/\s+/', Zend_Registry::get('img_servers'));
        $server     = $servers[$this->counter++];
        if ($this->counter >= count($servers)) {
            $this->counter = 0;
        }

        return $server.Zend_Registry::get($path).$img;
    }
}
