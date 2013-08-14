<?php

 /**
 * @file    config
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jun 27, 2012 6:20:27 PM
 * config of server
 */

$config['version'] = array(
    'js'=>'C05',
    'css'=>'C05',
);

define('UP_DOMAIN', 'wbbystatic');

$config['pdb'] = array(
    'dsn' => 'mysql:'.implode(';', array('host='.SAE_MYSQL_HOST_M, 'port='.SAE_MYSQL_PORT, 'dbname='.SAE_MYSQL_DB)),
    'dsn_s' => 'mysql:'.implode(';', array('host='.SAE_MYSQL_HOST_S, 'port='.SAE_MYSQL_PORT, 'dbname='.SAE_MYSQL_DB)),
    'username' => SAE_MYSQL_USER,
    'pwd' => SAE_MYSQL_PASS,
    'password' => SAE_MYSQL_PASS
);

// rely on sever
$config['default_avatar'] = ROOT . 'img/default_avatar.png';
$config['qq_login']['callback'] = 'http://weibobanyan.sinaapp.com/login';
