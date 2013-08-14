<?php

!defined('IN_KC') && exit('Access Denied');

/**
 * Description of Log
 *
 * @file    Log
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 27, 2012 11:35:47 AM
 */
class Log {

    public static function update($ip, $role_id) {
        //....
        Pdb::insert(array(
            'ip'=>$ip,
            'role'=>$role_id,
            'hit'=>1,
        ), 'actor', 'ON DUPLICATE KEY UPDATE hit=hit+1');
    }

    public static function listL() {
        //....
        return Pdb::fetchAll('role.name,actor.ip,actor.hit', 'role,actor', array(
            'role.id=actor.role'=>false
        ));
    }
}

?>
