<?php
!defined('IN_KC') && exit('Access Denied');
/**
 * @file    index
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jun 27, 2012 6:24:01 PM
 */

switch (get_set($_REQUEST['method'])) {
    default:
        break;
}

// scene
$scenes = Scene::ListS();

// twit list
extract(user_input($_GET, 'scene'));
$scene_id = $scene;
$conds = array();
if ($scene_id) {
    $conds['scene'] = $scene_id;
    $scene = new Scene($scene_id);
    $scene_info = $scene->getInfo();
}
$offset = get_set($_GET['offset'], 0);
$per_page = 30;

$paginate = new Paginate($per_page, Twit::getTotal($conds), $offset);

if ($control == 'hot') {
    $conds['hot'] = 1;
}
$twit_list = Twit::listT(array_merge(array('num'=>$per_page, 'offset'=>$offset), $conds));
$twit_list = array_map(function ($t) use($user_id) {
    $t['time'] = friendly_time2($t['time']);
    if ($t['origin']) {
        $t['origin']['time'] = friendly_time2($t['origin']['time']);
    }
    $twit = new Twit($t['id']);
    $t['can_up'] = $twit->canUpBy($user_id);
    $t['comments'] = $twit->getComments();
    return $t;
}, $twit_list);

if ($has_login) {
    $user = new User($user_id);
    $reminds = $user->getReminds();
    $reminds = array_filter($reminds, function ($r) {
        return !in_array($r['id'], explode(',', get_set($_SESSION['se_visited_twits'])));
    });
    $reminds_count = $reminds? count($reminds) : 0;
}

$page['scripts'][] = js_node('jquery.form');
$page['scripts'][] = js_node('widget');
$page['style'] = 'index';
