<?php

 

/**
 * Description of Xcon
 *
 * @file    Xcon
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 25, 2012 1:47:07 PM
 */
class Xcon {

    private $data = null;

    function __construct($str='') {
        $this->data = self::parse($str);
    }

    public static function parse($str='') {
        return explode(',', $str);
    }

    public function push($elem) {
        if (!in_array($elem, $this->data)) {
            $this->data[] = $elem;
        }
    }

    public function del($elem) {
        $pos = array_search($elem, $this->data);
        if ($pos !== false) {
            unset($this->data[$pos]);
        }
    }

    public function stringify() {
        return implode(',', $this->data);
    }
}

?>
