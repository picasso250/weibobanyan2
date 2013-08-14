<?php
!defined('IN_KC') && exit('Access Denied');
/**
 * @file    role
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 10:04:46 AM
 */

$conds = array(
    'num'=>100, // 默认100个？
    'view_from' => ($has_login? $user_id : 0),
);
extract(user_input($_GET, array('tag', 'keyword')));
if ($tag) {
    $conds['tag'] = $tag;
}
if ($keyword) {
    $conds['keyword'] = $keyword;
}
$role_list = Role::listR($conds);
if ($is_ajax) {
    out_json($role_list);
} else {
    $top_roles = Xcon::parse(get_set($_COOKIE['top_role']));
    $role_list = array_map(function ($role) use($top_roles) {
        $role['top'] = in_array($role['id'], $top_roles)? 1 : 0;
        return $role;
    }, $role_list);
    xcsort2($role_list, array('top', 'hot', 'id'));
}

$recent_roles = isset($_COOKIE['rh']) ? json_decode($_COOKIE['rh']) : array();
$recent_roles = array_map(function ($role_id) {
    return new Role($role_id);
}, $recent_roles);

function xcsort2(&$arr, $keys) {
    usort($arr, function ($a, $b) use($keys) {
        $none_zero = array_filter(array_map(function ($key) use($a, $b) {
            return $b[$key] - $a[$key];
        }, $keys), function ($d) {
            return $d != 0;
        });
        return (count($none_zero) == 0)? 0 : reset($none_zero);
    });
}

