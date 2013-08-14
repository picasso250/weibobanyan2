<?php

!defined('IN_KC') && exit('Access Denied');

/**
 * Description of Perm
 *
 * @file    Perm
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 26, 2012 7:59:19 PM
 */
class Perm {

    private $p = false;

    function __construct($user_kind) {
        $this->p = ($user_kind=='admin');
    }

    public static function getByUserKind($user_kind) {
        return new self($user_kind);
    }

    public function  check() {
        return $this->p;
    }
}

?>
