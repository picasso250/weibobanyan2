<?php

 
/**
 * @file    post
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 27, 2012 10:47:41 AM
 */

switch (get_set($_REQUEST['method'])) {
    case 'know':
        $_SESSION['se_know_post'] = 1;
        // header 204???
        break;
    default:
        break;
}
exit;

?>