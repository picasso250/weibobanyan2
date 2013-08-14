<?php
!defined('IN_KC') && exit('Access Denied');
/**
 * @file    common
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jun 30, 2012 10:38:22 AM
 */

if (isset($_SERVER['HTTP_APPNAME'])) {
    define('ON_SERVER', TRUE);
    define('ON_SAE', 1);
} else {
    define('ON_SERVER', FALSE);
    define('ON_SAE', 0);
}

if (!ON_SERVER) { // server中的配置不同
    define('DEBUG', TRUE);
    define('ROOT', '/');
} else {
    define('DEBUG', FALSE);
    define('ROOT', '/');
}

$config['pdb'] = array(
    'dsn' => 'mysql:host=localhost;dbname=weibo',
    'username' => 'root',
    'pwd' => '',
    'password' => ''
);

// === 以下，大部分，server 和 local 相同

// 网址=>控制器
$config['controls'] = array(
    'index'    => 'index',
    'hot'      => 'index',
    'role'     => 'role',
    'twit'     => 'twit',
    'help'     => 'about',
    'log'      => 'about',
    'credit'   => 'about',
    'about'    => 'about',
    'todo'     => 'about',
    'login'    => 'login',
    'logout'   => 'login',
    'scene'    => 'scene',
    'longtian' => 'longtian',
    'post'     => 'post',
    'ajax'     => 'ajax',
);

$config['qq_login'] = array(
    'app_id'=>'100289788',
    'app_key'=>'e7799cf594916269e276b80d100d85f6',
    'scope'=>implode(',', array('get_user_info')),
    'callback'=> !isset($config['server'])?'http://weibobanyan.sinaapp.com/fake_weibo/login':'http://weibobanyan.sinaapp.com/login',
);

$config['default_avatar'] = ROOT . 'img/default_avatar.png';

$ip_ban = array(
//    '121.0.29.193', // 我是苍井空
);
