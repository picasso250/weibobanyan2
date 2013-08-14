<?php
!defined('IN_KC') && exit('Access Denied');
?>
<div class="comment" style="">
    <ul class="comment" style="<?php echo count($comments) > 0 ? '' : 'display: none'; ?>">
        <?php 
        include _block('comment_list');
        ?>
    </ul>
    
    <?php if ($role_id): ?>
    <form class="post-comment" method="post" action="<?php echo ROOT.'twit/'.$t['id']; ?>">
        <input type="hidden" name="method" value="comment" />
        <div class="comment-form row-fluid shrink">
          <div class="span1">
            <img class="avatar img-polaroid" src="<?php echo $role_info['avatar']; ?>" />
          </div>
          <div class="span11">
            <textarea name="text" placeholder="说点什么吧" class="span12"></textarea>
          </div>
          <input type="submit" value="评论" class="comment-btn btn pull-right" />
        </div>
    </form>
    <?php endif; ?>
</div>