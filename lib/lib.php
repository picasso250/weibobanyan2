<?php
!defined('IN_KC') && exit('Access Denied');
/**
 * @file    lib
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jun 27, 2012 6:27:14 PM
 * all kinds of functions
 */

function get_set(&$param, $or='') {
    return isset($param)? $param : $or;
}

function fill_empty(&$var, $filling) {
    if (empty($var)) {
        $var = $filling;
    }
}

function explo_path($path) {
    $ret = explode('/', $path);
    if (count($ret)==0) {
        $ret = array('index');
    }
    // if any of elem of array is empty, replace it with 'index'
    foreach ($ret as $k=>$v) {
        if ($v==='') {
            $ret[$k] = 'index';
        }
    }
    return $ret; // TODO 可以缩减
}

/**
 *
 * @global type $config
 * @param type $src
 * @param type $code
 * @return type
 * @version 0.3
 */
function js_node($src='', $code='') {
    $src_str = $src? ' src="' . ROOT . 'js/'.$src.'.js?v='.static_source_version('js').'"' : '';
    return '<script type="text/javascript"'.$src_str.'>'.$code.'</script>';
}

/**
 *
 * @global type $config
 * @param type $src
 * @param type $type
 * @return type
 * @version 0.3
 */
function css_node($src='', $type='css') {
    $rel = 'rel="stylesheet'.($type!='css'?'/'.$type:'').'"';
    $href = 'href="'.ROOT.'css/'.$src.'.'.$type.'?v='.static_source_version('css').'"';
    $type = 'type="text/css"';
    return "<link $rel $type $href />";
}

function js_var($var_name, $arr) {
    return js_node('', $var_name.'='.json_encode($arr));
}

/**
 *
 * @global type $config
 * @return \type
 * @throws Exception
 * @version 0.2
 */
function static_source_version($type='css') {
    global $config; // TODO
    if (DEBUG) {
        return time();
    } else if (ON_SERVER) {
        return $config['version'][$type];
    } else {
        throw new Exception;
    }
}

/** translate Y-m-d to xx之前 or 今天XX
 *
 * @param type $date_time_str 形如 Y-m-d H:i:s （sql中获得的DateTime类型即可）
 */
function friendly_time2($date_time_str) {
    $date_time = new DateTime($date_time_str);
    $nowtime = new DateTime();
    $diff = $nowtime->diff($date_time);
    if ($diff->y==0 && $diff->m==0 && $diff->d==0) { // 同一天
        if ($diff->h<1) // 一个小时以内
            if ($diff->i==0) // 一分钟以内
                return '刚刚';
            else
                return $diff->i.'分钟前'; // minutes
        else
            return '今天';
    } else {
        return current(explode(' ', $date_time_str));
    }
}

function d($param, $var_dump=0) {
    global $config;
    if (DEBUG) {
        echo "<p><pre>\n";
        if ($var_dump) {
            var_dump($param);
        } else {
            print_r($param);
        }
        echo "</p></pre>\n";
    } else {
        return;
    }
}

function user_input($arr, $para_list) {
    if (!is_array($para_list)) {
        $para_list = array($para_list);
    }
    $ret = array();
    foreach ($para_list as $p) {
        $ret[$p] = trim(get_set($arr[$p]));
    }
    return $ret;
}


/* image upload helpers */

/**
 * what is this?
 * @param type $file_content
 * @param type $crop
 * @param type $width
 * @param type $height
 * @param type $new_width
 * @param type $new_height
 * @return type
 * @throws Exception
 */
function image_resize ($file_content, $crop, $width, $height, $new_width, $new_height) {
    if ($new_width < 1 || $new_height < 1) {
        throw new Exception('specified size too small');
    } else if ($width<$new_width || $height<$new_height) {
        throw new Exception('image size too small', 42);
    } else {
        $dst = imagecreatetruecolor($new_width, $new_height);
        $src_x = 0;
        $src_y = 0;
        if ($crop) {
            $ratio = $width / $height;
            $new_ratio = $new_width / $new_height;
            if ($ratio > $new_ratio) {
                $old_width = $width;
                $width = ceil($new_ratio * $height);
                $src_x = ($old_width - $width) / 2;
            } else if ($ratio < $new_ratio) {
                $old_height = $height;
                $height = ceil($width / $new_ratio);
                $src_y = ($old_height - $height) / 2;
            }
        }
        $s = imagecopyresampled($dst, $file_content, 0, 0, $src_x, $src_y, $new_width, $new_height, $width, $height);
        return $dst;
    }
}

function image_file_resize($tmp_img_file, $image_type, $crop, $new_width, $new_height) {
    list($width, $height) = getimagesize($tmp_img_file);
    $image_type_map = array(
        'jpg' => 'jpeg',
        'jpeg' => 'jpeg',
        'pjpeg' => 'jpeg',
        'png' => 'png',
        'x-png' => 'png');
    $image_type = strtolower($image_type);
    if (isset($image_type_map[$image_type]))
        $image_type = $image_type_map[$image_type];
    $src = call_user_func('imagecreatefrom' . $image_type, $tmp_img_file);
    try {
        $dst = image_resize($src, $crop, $width, $height, $new_width, $new_height);
    } catch (Exception $e) {
        throw $e;
    }

    ob_start();
    call_user_func('image' . $image_type, $dst);
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

// from file
function make_image2($imagefile, $opt = array())
{
    // deault option
    $opt = array_merge(array(
        'crop' => 0,
        'resize' => 0,
        'width' => 50,
        'height' => 50,
        'list' => null,
    ), $opt);
    
    $extention = $image_type = end(explode('.', $imagefile));

    $tmp_img = $imagefile;
    
    return _make_image($tmp_img, $image_type, $extention, $opt);
}

/**
 * @param string $tmp_img 文件内容
 */
function _make_image($tmp_img, $image_type, $extention, $opt)
{
    $resize = $opt['resize'];
    $opt_list = $opt['list'];
    if (!$opt_list) {
        $opt_list = array($opt);
    }

    $ret = array();
    foreach ($opt_list as $opt_) {
        if ($resize) {
            $content = image_file_resize($tmp_img, $image_type, $opt_['crop'], $opt_['width'], $opt_['height']);
        } else {
            $content = file_get_contents($tmp_img);
        }
        $file_name = uniqid() . '.' . $extention;
        $ret[] = write_upload($content, $file_name);
    }
    return count($ret) === 1 ? reset($ret) : $ret;
}

/**
 * main function
 * @param type $image is xx in $_FILES['xx']
 * @param type $opt resize crop width height
 * @return string url of the final img
 * @throws Exception
 */
function make_image($image, $opt=array()) {
    
    // default option
    $opt = array_merge(array(
        'crop' => 0,
        'resize' => 0,
        'width' => 50,
        'height' => 50,
        'list' => null,
    ), $opt);
    
    $image = $_FILES[$image];
    
    $arr = explode('/', $image['type']);
    $file_type = reset($arr);
    $image_type = end($arr);
    if ($file_type == 'image') {
        
        $extention = file_ext($image['name']);
        
        $tmp_img = $image['tmp_name'];

        return _make_image($tmp_img, $image_type, $extention, $opt);
    } else { // maybe throw??
        return '';
    }
}

// write file content to dst
function write_upload($content, $file_name) {
    if (ON_SAE) {
        $up_domain = UP_DOMAIN;
        $s = new SaeStorage();
        $s->write($up_domain , $file_name , $content);
        return $s->getUrl($up_domain ,$file_name);
    } else {
        $root = 'data/';
        if (!file_exists($root)) {
            mkdir($root);
        }
        $dst_root = $root .'upload/';
        if (!file_exists($dst_root)) {
            mkdir($dst_root);
        }
        $year_month_folder = date('Ym');
        $path = $year_month_folder;
        if (!file_exists($dst_root.$path)) {
            mkdir($dst_root.$path);
        }
        $date_folder = date('d');
        $path .= '/'.$date_folder;
        if (!file_exists($dst_root.$path)) {
            mkdir($dst_root.$path);
        }
        $path .= '/'.$file_name;
        file_put_contents($dst_root.$path, $content);
        return ROOT . 'data/upload/' . $path;
    }
}

function file_ext($file_name) {
    return substr(strrchr($file_name, '.'), 1);
}

function out_json($arr, $quit=true) {
    echo json_encode($arr);
    if($quit){
        exit;
    }
}

function redirect($url='/') {
    header('Location:'.$url);
    exit();
}

function _tpl ($file_name) {
    $php_file = 'template/'.$file_name.'.php';
    $html_file = 'template/'.$file_name.'.html';
    $phtml_file = 'template/'.$file_name.'.phtml';
    if (file_exists($phtml_file)) {
        return $phtml_file;
    }
    if (file_exists($html_file)) {
        return $html_file;
    } else {
        return $php_file;
    }
}

function _controller ($file_name) {
    return AROOT.'controller/'.$file_name.'.php';
}

function _model ($name) {
    return 'model/'.$name.'.php';
}

function _lib ($name) {
    return 'lib/'.$name.'.php';
}

function _block($name) {
    return _tpl('block/'.$name);
}

function sae_log($msg){
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}

function _get($key, $or = null) 
{
    return isset($_GET[$key]) ? $_GET[$key] : $or;
}
function _post($key, $or = null) 
{
    return isset($_POST[$key]) ? $_POST[$key] : $or;
}
function _req($key, $or = null) 
{
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $or;
}

function _last_controller()
{
    $ifs = get_included_files();
    $i = count($ifs);
    while ($i && !preg_match('%[/\\\\]controller[/\\\\](.+)\.php$%', $ifs[--$i], $matches)) {
        ;
    }
    return $matches[1];
}
