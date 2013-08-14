<?php
/**
 * @file    index
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jun 30, 2012 10:38:22 AM
 * app logic
 * 此框架由王霄池纯粹手写而成，当然参照了不少鸡爷的框架
 */

// 打开错误提示
ini_set('display_errors', true); // 在 SAE 上 ini_set() 不起作用，但也不会报错
error_reporting(E_ALL | E_STRICT);

// define('IN_KC', 1);
define('AROOT', __DIR__.'/');

require AROOT.'lib/lib.php';
require AROOT.'config/common.php';
if (ON_SERVER) {
    require 'config/server.php'; // sever中的配置会覆盖common中的配置
}

// load model classes and more
require 'vendor/idiorm.php';
require 'vendor/paris.php';
spl_autoload_register(function ($name) {
    if (preg_match('/^XcFrameWork\b/', $name)) {
        require AROOT.'vendor/'.str_replace('\\', '/', $name).'.php';
        return;
    }
    if (file_exists(_lib($name))) {
        require_once _lib($name);
    } elseif (file_exists(_model($name))) {
        require_once _model($name);
    }
});

require AROOT.'init.php'; // 变量的初始化

use XcFrameWork\XcWebApp;

$app = new XcWebApp();
$app->config();
$app->run();

include _controller('init');

if (isset($force_redirect)) { // 强制跳转 这个在整站关闭的时候也很有用啊
    include _controller($force_redirect);
} else {
    // 查看是否是合法的$control，如是，则包含文件，如否，则跳转向404页面
    $control = isset($config['controls'][$control]) ? $config['controls'][$control] : $control;
    if (file_exists(_controller($control))) {
        include _controller($control);
    }else {
        // 404
        include _controller('page404');
    }
}
$template = _tpl(_last_controller());
include _tpl('master');

var_dump(get_included_files());
