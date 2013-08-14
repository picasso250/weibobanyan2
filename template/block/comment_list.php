<?php
!defined('IN_KC') && exit('Access Denied');
?>
<?php foreach ($comments as $c): ?>
<li class="comment">
  <span class="name-wrap">
    <img src="<?php echo $c['avatar']?:$config['default_avatar']; ?>" />
  </span>
  <a href="<?php echo $rooturl . 'role/' . $c['author_id']; ?>" class="name-wrap">
    <?php echo $c['author']; ?>
  </a>
  <?php if ($c['is_v']): ?>
    <span class="verify"><?php echo ($c['is_v'])?'V':''; ?></span>
  <?php endif ?>
  <span>：</span>
  <span><?php echo $c['text']; ?></span>
  <span class="pull-right time"><?php echo $c['time'] ?></span>
</li>
<?php endforeach ?>