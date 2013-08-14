<?php
 
/**
 * @file    role
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 10:04:46 AM
 */

if (count($_FILES) == 0 || !isset($_FILES['avatar'])) {
    die;
}

$fpath = make_image(
    'avatar', 
    array(
        'width' => 74,
        'height' => 74,
        'crop' => 1,
        'resize' => 1,
    )
);
echo json_encode(array('path' => $fpath));
exit;

