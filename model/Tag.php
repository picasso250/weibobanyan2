<?php

!defined('IN_KC') && exit('Access Denied');

/**
 * Description of Tag
 *
 * @file    Tag
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 27, 2012 3:01:01 PM
 */
class Tag extends Model {
    public static function getIdByText($tag_text) {
        $table = 'role_tag';
        //....
        $r = Pdb::fetchRow('id', $table, array(
            'tag=?'=>$tag_text
        ));
        if ($r) {
            return $r['id'];
        } else {
            Pdb::insert(array('tag'=>$tag_text), $table);
            return Pdb::lastInsertId();
        }
    }
}
