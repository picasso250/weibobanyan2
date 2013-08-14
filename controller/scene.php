<?php

 
/**
 * @file    scene
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 26, 2012 11:00:55 AM
 */

$id = get_set($uri_arr[1]);

$validate_scene = $id && is_numeric($id);
if ($validate_scene) {
    $scene = new Scene($id);
}

switch ($method) {
    case 'add':
        $set_title = '创建场景';
        $template = _tpl('scene_add_edit');
        $btn_caption = '创建场景';
        $description = '';
        if ($is_post) {
            extract(user_input($_POST, array('name', 'description')));
            if ($name) {
                Scene::creat($name, $description);
                redirect($rooturl.'scene'); // config root url=>rooturl
            }
        }
        break;
    case 'edit':
        if ($validate_scene) {
            $set_title = '编辑场景';
            $template = _tpl('scene_add_edit');
            $btn_caption = '就此更改';
            extract($scene->getInfo());
            if ($is_post) {
                $para = user_input($_POST, array('description'));
                $scene->edit($para);
                redirect($rooturl.'scene');
            }
        }
        break;
    case 'del':
        if ($validate_scene) {
            $scene->prepareDel();
        }
        redirect($rooturl);
    case 'cancel_del':
        if ($validate_scene) {
            $scene->prepareDel(0);
        }
        redirect($rooturl);
    case 'confirm_del':
        if ($validate_scene && $perm->check('scene', 'del')) {
            if ($scene->del()) {
                die('del success');
            }
        }
        redirect($rooturl);
    default:
        break;
}

if (!$validate_scene) {
    $scenes = Scene::ListS(array('num' => 1000));
}
