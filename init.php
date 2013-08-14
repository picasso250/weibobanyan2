<?php
 
// load model classes and more
require 'vendor/idiorm.php';
spl_autoload_register(function ($name) {
    if (file_exists(_lib($name))) {
        require_once _lib($name);
    } elseif (file_exists(_model($name))) {
        require_once _model($name);
    }
});

$root_path = __DIR__.'/'; // 没用啊。。。

// 变量初始化
$show_header = 1; // 这种东西是否应该扔到config中？或者集合到$show变量里
$show_footer = 1;

$arr = explode('?',$_SERVER['REQUEST_URI']);
$request_uri = substr($arr[0], strlen(ROOT));
$is_ajax = get_set($_REQUEST['is_ajax']) || (strtolower(get_set($_SERVER['HTTP_X_REQUESTED_WITH'])) == strtolower('XMLHttpRequest'));
$is_post = strtolower(get_set($_SERVER['REQUEST_METHOD'])) == 'post'; // 有用吗？

$uri_arr = explo_path($request_uri);
$control = $uri_arr[0];

$page = array(
    'head'=>array(), // 在head里面的语句
    'scripts'=>array(), // 页面底部的script
); // 关于这个页面的变量

// ban ip
$ip = $_SERVER['REMOTE_ADDR'];
if (in_array($ip, $ip_ban)) {
    exit('Sorry, Your IP is of malice.');
}
