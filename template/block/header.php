<?php

!defined('IN_KC') && exit('Access Denied');
/**
 * @file    header
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jun 30, 2012 11:27:00 AM
 */
?>
<h1 title="伪博扮演 首页">
  <a href="<?php echo ROOT; ?>">
    <span class="text">伪博扮演</span>
  </a>
</h1>
<div class="control">
    <?php if ($role_id) { ?>
    <span>正在扮演</span>
    <a href="<?php echo $rooturl.'role/'.$role_id; ?>">
        <img src="<?php echo $role_info['avatar']; ?>" />
        <span class="name"><?php echo $role_info['name'].($role_info['is_v']?'<span class="verify">V</span>':''); ?></span>
    </a>
    <a href="<?php echo ROOT; ?>role">角色列表</a>
    <?php } else { ?>
    <a href="<?php echo ROOT; ?>role">扮演角色</a>
    <?php } ?>
    <a href="help">Help</a>
</div>
