<?php
 
/**
 * @file    scene
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 26, 2012 10:52:37 AM
 */
?>
<div class="scene-list">
    <ul>
        <?php foreach ($scenes as $scene) { ?>
        <li>
            <h3 href="<?php echo $rooturl.'scene/'.$scene['id']; ?>"><?php echo $scene['name']; ?></h3>
            <div class="description"><?php echo $scene['description']; ?></div>
            <a href="<?php echo $rooturl.'scene/'.$scene['id'].'?method=edit'; ?>">编辑</a>
            <a class="goto" href="<?php echo $rooturl.'index?scene='.$scene['id']; ?>">进入场景&gt;</a>
        </li>
        <?php } ?>
    </ul>
</div>