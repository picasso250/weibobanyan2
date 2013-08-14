<?php
 
/**
 * @file    twit
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 4:26:14 PM
 */

$id = get_set($uri_arr[1]);


$validate_twit = $id && is_numeric($id);
if ($validate_twit) {
    $twit = new Twit($id);
}

switch (get_set($_REQUEST['method'])) {
    case 'post': // add
        extract(user_input($_POST, array('text', 'scene', 'image_src')));
        if ($text || ($image_src)) {
            $role = new Role($role_id);
            $role->tweet($text, $image_src, $scene);
        }
        redirect('index?scene='.$scene);
        break;
    case 'edit':
        $arr = (user_input($_POST, array('comment_num', 'retweet_num')));
        if ($arr) {
            $twit->edit($arr);
        }
        break;
    case 'comment':
        if ($is_post) {
            extract(user_input($_POST, 'text'));
            if ($text && isset($twit)) {
                $twit->comment($text, $role_id);
                $stat = 1;
                $msg = '';
                if ($is_ajax) {
                    out_json(compact('msg', 'stat'));
                }
            }
        } else if ($is_ajax) { // get
            $comments = $twit->getComments();
            $comments = array_map(function ($e) use($config) {
                fill_empty($e['avatar'], $config['default_avatar']);
                return $e;
            }, $comments);
            include _block('comment_list');
            exit;
        }
        break;
    case 'retweet':
        extract(user_input($_POST, 'text'));
        if ($text) {
            $twit->retweet($text, $role_id);
            redirect(ROOT);
        }
        break;
    case 'del':
        $twit->prepareDel();
        redirect(ROOT);
        break;
    case 'cancel_del':
        $twit->prepareDel(0);
        redirect(ROOT);
        break;
    case 'confirm_del':
        if ($perm->check('twit', 'del')) {
            if ($twit->del()) {
                exit('del success');
            }
        }
        redirect(ROOT);
        break;
    case 'count':
        if ($is_ajax) {
            $para = user_input($_GET, 'interval');
            out_json(array('num'=>Twit::count($para)));
        }
        break;
    case 'up':
        if ($validate_twit) {
            $twit->up($user_id);
            if ($is_ajax) {
                exit();
            } else {
                redirect($rooturl);
            }
        }
    default:
        break;
}

if ($validate_twit) { // 可以废弃了??
    $info = $twit->getInfo();
    $comments = $twit->getComments();
} else {
    $conds = user_input($_GET, 'num');
    $twit_list = Twit::listT($conds);
    $twit_list = array_map(function ($t) use($user_id) {
        $t['time'] = friendly_time2($t['time']);
        if ($t['origin']) {
            $t['origin']['time'] = friendly_time2($t['origin']['time']);
        }
        $twit = new Twit($t['id']);
        $t['can_up'] = $twit->canUpBy($user_id);
        return $t;
    }, $twit_list);
    if ($is_ajax) out_json($twit_list);
}

// ??
if ($validate_twit) {
    $visited_twits = explode(',', get_set($_SESSION['se_visited_twits']));
    array_push($visited_twits, $id);
    $_SESSION['se_visited_twits'] = implode(',', $visited_twits);
}

