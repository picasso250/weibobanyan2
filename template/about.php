<?php

 
/**
 * @file    help
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 18, 2012 10:18:27 PM
 */
?>
<?php if ($control == 'help') { ?>
<div class="help">
    <p>我曾经看过琢磨先生的作品：</p>
    <img src="<?php echo ROOT; ?>img/sanguo.jpg" />
    <p>或许你心中也对那些人物喜爱有加。那么你也来制作一段假微博吧。或者卖萌，或者卖傻，都可以做一段故事。</p>
    <p>你可以<a href="role">创建角色</a>，你可以<a href="role">代入角色</a>，<a href="<?php echo ROOT; ?>">说他们想说的话，说你想让他们说的话</a>。在这里，你是这个世界的导演。</p>
</div>
<?php } else if ($control == 'about') { ?>
<div class="about">
    <p>我曾经看过琢磨先生的作品：</p>
    <img src="<?php echo ROOT; ?>img/sanguo.jpg" />
    <p>或许你心中也对那些人物喜爱有加。那么你也来制作一段假微博吧。或者卖萌，或者卖傻，都可以做一段故事。</p>
    <p>你可以<a href="role">创建角色</a>，你可以<a href="role">代入角色</a>，<a href="<?php echo ROOT; ?>">说他们想说的话，说你想让他们说的话</a>。在这里，你是这个世界的导演。</p>
</div>
<?php } else if ($control == 'credit') { ?>
<div class="credit">
    <dl>
        <dt>制作人员</dt>
        <dd>创意：鸡爷</dd>
        <dd>开发：小池</dd>
        <dd>超级亲友团：超君、杨军、军军、林仔、吕某莹，小F、马某、王某莹等，如未尽，或者需要改名字，请联系我……</dd>
        <dd>运营团队：王某莹</dd>
    </dl>
</div>
<?php } else if ($control == 'log') { ?>
<div class="log">
    <ul>
        <li>2012-07-27: 暂时撤销评论功能，角色可以加标签了</li>
        <li>2012-07-22: 支持修改角色头像</li>
        <li>2012-07-19: 支持手机版本，首页内容区域变宽，新增转发功能</li>
        <li>2012-07-18: 我不记得了</li>
    </ul>
</div>
<?php } else if ($control == 'todo') { ?>
<div class="todo">
    <p>下面是即将上线的功能，如果您有什么意见，请直接发微博告诉<a href="http://weibo.com/xiaochi2">@王霄池</a></p>
    <ul>
        <li>标志自从你上次访问过以后新到的微博(via cookie)</li>
        <li>图片自动缩小</li>
        <li>评论提醒</li>
        <li>角色最近的伪博</li>
        <li>角色热度</li>
        <li>场景热度</li>
    </ul>
</div>
<?php } ?>