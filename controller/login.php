<?php

!defined('IN_KC') && exit('Access Denied');
/**
 * @file    login
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 2:30:41 PM
 */

if ($control=='logout') {
    session_unset();
    redirect(ROOT);
}

$platform = 'QQ';
$qq_login = new QqLogin($config['qq_login']);
$qq_href = $qq_login->getLoginHref();

if ($qq_login->isCalled()) {
    $qq_login->getAccessToken($_GET['code']);
    $qq_openid = $qq_login->getOpenId();

    $user = User::get($platform, $qq_openid);
    if ($user === false) { // if not exist
        $user = User::createFromOpenId($platform, $qq_openid);
    }
    // get info
    $info = $qq_login->getInfo();
    // login
    $_SESSION['se_user_platform'] = $platform;
    $_SESSION['se_user_id'] = $user->getId();
    $_SESSION['se_user_name'] = $info['name'];
    $_SESSION['se_user_avatar'] = $info['avatar'];

    // update time  and so on
    $user->active();

    redirect(ROOT);
}

?>