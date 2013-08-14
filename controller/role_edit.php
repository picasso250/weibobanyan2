<?php
 
/**
 * @file    role
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 10:04:46 AM
 */

$role = Role::get(_post('id'));
if (_post('field')) {
    $role->set(_post('field'), _post('value'));
    $rs = $role->save();
    if ($rs) {
        if (_post('field') == 'is_v') {
            include _tpl('role_name');
        }
    }
}
exit;