<?php
 
/**
 * @file    role
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 10:04:46 AM
 */


$base_url = ROOT.'role/'; // TODO

extract(user_input($_GET, 'name'));
if ($name) {
    $id = Role::getIdByName($name);
    if ($is_ajax) {
        out_json(array('state'=>($id? 1 : 0)));
    }
} else {
    $id = get_set($uri_arr[1]);
}
$validate_role = $id && is_numeric($id);
if ($validate_role) {
    $role = new Role($id);
}

switch (get_set($_REQUEST['method'])) {
    case 'add':
        extract(user_input($_POST, 'name'));
        if ($name) {
            if ($role = Role::hasName($name)) {
                redirect($rooturl.'role/'.$role->id);
            }
            try { // 这里有 try，但别处没有try，这里是严谨而无趣的地方。。。
                $role = Role::add(array('name' => $name));
                $role->addToHistory();
                redirect($rooturl . 'role/' . $role->id);
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        break;
    case 'add_tag':
        extract(user_input($_POST, 'text'));
        if ($text && $validate_role) {
            $role->addTag($text);
        }
        break;
    case 'play':
        if ($validate_role) {
            $_SESSION['se_role_id'] = $id;
            $role->hot();
            $role->addToHistory();
            redirect(ROOT);
        }
        break;
    case 'edit':
        extract(user_input($_POST, array('is_v'))); // id will cover?
        $is_v = ($is_v=='on' || $is_v=='1')? 1 : 0;
        $avatar_img = get_set($_FILES['avatar']);
        if ($avatar_img && $avatar_img['name']) {
            $avatar = make_image('avatar', array(
                'resize'=>1,
                'crop'=>1,
                'width'=>50,
                'height'=>50
            ));
        }
        if (isset($role)) {
            $role->edit(compact('is_v', 'avatar'));
        }
        redirect($base_url);
        break;
    case 'watch':
        if ($validate_role && $has_login) {
            $user = new User($user_id);
            $user->watch($id);
        }
        break;
    case 'unwatch':
        if ($validate_role && $has_login) {
            $user = new User($user_id);
            $user->unwatch($id);
        }
        break;
    case 'top':
        if ($validate_role) {
            $role->top();
        }
        redirect($base_url);
        break;
    case 'untop':
        if ($validate_role) {
            $role->untop();
        }
        redirect($base_url);
        break;
    default:
        break;
}

if ($validate_role) {
    $info = $role->getInfo($has_login?$user_id:0);
    $role_tags = $role->getTags();
    $recent_twit_num = $role->countRecentTwit();
    $twits = $role->recentTwit();
    $twits = array_map(function ($t) use($user_id) {
        $t['time'] = friendly_time2($t['time']);
        if ($t['origin']) {
            $t['origin']['time'] = friendly_time2($t['origin']['time']);
        }
        $twit = new Twit($t['id']);
        $t['can_up'] = $twit->canUpBy($user_id);
        $t['comments'] = $twit->getComments();
        return $t;
    }, $twits);
} else {
    include 'role_list.php';
}

