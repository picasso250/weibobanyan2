<?php
 
/**
 * @file    twit
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 17, 2012 4:38:13 PM
 */
?>
<div class="twit">
    <?php
    $t = $info;
    echo js_var('jiathis_config', array(
        'title'=>$info['author'].'的伪博扮演',
        'summary'=>$info['text'],
    ));
    ?>
    <div class="control">
        <a class="retweet-btn">转发（<?php echo $t['retweet_num']; ?>）</a>
        <a class="comment-btn">评论（<?php echo $t['comment_num']; ?>）</a>
    </div>

    <!-- JiaThis Button BEGIN -->
    <div id="ckepop">
        <span class="jiathis_txt">分享到：</span>
        <a class="jiathis_button_tools_1"></a>
        <a class="jiathis_button_tools_2"></a>
        <a class="jiathis_button_tools_3"></a>
        <a class="jiathis_button_tools_4"></a>
        <a href="http://www.jiathis.com/share" class="jiathis jiathis_txt jiathis_separator jtico jtico_jiathis" target="_blank">更多</a>
        <a class="jiathis_counter_style"></a>
    </div>
    <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=1341805713461476" charset="utf-8"></script>
    <!-- JiaThis Button END -->

    <div class="comment">
        <?php if ($role_id) { ?>
        <form action="?" method="post">
            <div class="comment-form">
                <input type="hidden" name="method" value="comment" />
                <textarea name="text"></textarea>
                <input type="submit" value="评论" class="btn" />
            </div>
        </form>
        <?php } else { ?>
        <div>先<a href="<?php echo $rooturl.'role'; ?>">选择角色</a>再评论</div>
        <?php } ?>
        <?php if (count($comments) == 0) { ?>
        <div>还没有评论，沙发空着呢</div>
        <?php } else { ?>
        <ul class="comment">
            <?php foreach ($comments as $c) { ?>
            <li class="comment">
                <a href="<?php echo $rooturl.'role/'.$c['role_id']; ?>">
                    <img src="<?php echo $c['avatar']?:$config['default_avatar']; ?>" />
                    <span><?php echo $c['author']; ?></span>
                    <?php if ($c['is_v']) { ?><span class="verify">V</span><?php } ?>
                </a>
                <span>：<?php echo $c['text']; ?></span>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
    </div>
</div>

