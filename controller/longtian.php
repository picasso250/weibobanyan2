<?php
!defined('IN_KC') && exit('Access Denied');
/**
 * @file    longtian
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 26, 2012 6:19:24 PM
 */

if ($perm->check() || DEBUG) {
    $del_twits = Twit::listT(array('will_del'=>1));
    $del_scenes = Scene::ListS(array('will_del'=>1));

    $log = Log::listL();
}
