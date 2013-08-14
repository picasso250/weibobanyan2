<?php
 
/**
 * @file    twit
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 4:26:14 PM
 */
if (isset($_FILES['image']['name'])) {
    $fpath = make_image('image');
    echo json_encode(array('path' => $fpath));
}
exit;
