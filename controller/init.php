<?php
!defined('IN_KC') && exit('Access Denied');
/**
 * @file    init
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jun 30, 2012 11:50:49 AM
 */

$page['description'] = '伪博扮演，扮演整个世界';
$page['keywords'] = array('伪博','扮演');

$rooturl = ROOT; // TODO
$method = get_set($_REQUEST['method']);

Pdb::setConfig($config['pdb']);
ORM::configure($config['pdb']['dsn']);
ORM::configure('username', $config['pdb']['username']);
ORM::configure('password', $config['pdb']['password']);
if (DEBUG) {
    ORM::configure('logging', true);
}

require_once AROOT.'lib/db.function.php';
require_once AROOT.'lib/core.class.php';

$role_id = get_set($_SESSION['se_role_id']);
if ($role_id) {
    $role = new Role($role_id);
    $role_info = $role->getInfo();
    fill_empty($role_info['avatar'], $config['default_avatar']);
}

$has_login = 0; // 那就总是lognin？？
$user_id = get_set($_SESSION['se_user_id']);
if ($user_id && isset($_SESSION['se_user_name'])) {
    $has_login = 1;
    $user = new User($user_id);
    $user_info = $user->getInfo();
} else {
    $cookie_id = get_set($_COOKIE['xc_id']);
    if (!$cookie_id) {
        $cookie_id = md5(uniqid());
        setcookie('xc_id', $cookie_id, time() + 3600*24*180); // 半年
    }
    $platform = 'cookie';

    $user = ORM::for_table('open_id')
        ->where('platform', $platform)
        ->where('open_id', $cookie_id)
        ->select('user')
        ->find_one();
    if ($user === false) { // if not exist
        $user = User::createFromOpenId($platform, $cookie_id);
    } else {
        $user = User::orm()->find_one($user->user);
    }
    $user_id = $_SESSION['se_user_id'] = $user->get('id'); // 确保 $user_id有值
}

$perm = Perm::getByUserKind(get_set($user_info['kind']));
