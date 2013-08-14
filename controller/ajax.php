<?php

!defined('IN_KC') && exit('Access Denied');
/**
 * @file    help
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 18, 2012 10:23:53 PM
 */

$count = Twit::search()->by('(UNIX_TIMESTAMP(`time`)+3*60) > UNIX_TIMESTAMP()')->count();
echo $count;
exit;