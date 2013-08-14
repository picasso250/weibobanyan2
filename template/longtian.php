<?php

 
/**
 * @file    longtian
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 26, 2012 6:23:12 PM
 */
?>
<div class="admin">
    <h2>风评要删除的伪博</h2>
    <ul>
        <?php foreach ($del_twits as $tw) { ?>
        <li>
            <div>作者：<?php echo $tw['author']; ?></div>
            <div>内容：<?php echo $tw['text']; ?></div>
            <div>评论数：<?php echo $tw['comment_num']; ?></div>
            <div>转发数：<?php echo $tw['retweet_num']; ?></div>
            <a href="<?php echo $rooturl.'twit/'.$tw['id'].'?method=confirm_del'; ?>">彻底删除</a>
            <a href="<?php echo $rooturl.'twit/'.$tw['id'].'?method=cancel_del'; ?>">打回</a>
        </li>
        <?php } ?>
    </ul>
    <h2>风评要删除的场景</h2>
    <ul>
        <?php foreach ($del_scenes as $s) { ?>
        <li>
            <div>名称：<?php echo $s['name']; ?></div>
            <div>描述：<?php echo $s['description']; ?></div>
            <a href="<?php echo $rooturl.'scene/'.$s['id'].'?method=confirm_del'; ?>">彻底删除</a>
            <a href="<?php echo $rooturl.'scene/'.$s['id'].'?method=cancel_del'; ?>">打回</a>
        </li>
        <?php } ?>
    </ul>
    <h2>日志记录（演员表）</h2>
    <table>
        <tr><th>IP</th><th>角色</th><th>伪博条数</th></tr>
        <?php foreach ($log as $l) { ?>
        <tr><td><?php echo $l['ip']; ?></td><td><?php echo $l['name']; ?></td><td><?php echo $l['hit']; ?></td></tr>
        <?php } ?>
    </table>
</div>
