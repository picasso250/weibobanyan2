<?php
!defined('IN_KC') && exit('Access Denied');
/**
 * @file    master
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>伪博扮演</title>
        <meta property="qc:admins" content="245072776127216116631611006375" />
        <meta name="description" content="<?php echo get_set($page['description']); ?>" />
        <meta name="keywords" content="<?php echo implode(', ', get_set($page['keywords'], array())); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <?php
        echo css_node('bootstrap.min');
        $ext = 'css';// ON_SERVER ? 'css' : 'less';
        echo get_set($page['style'])? css_node($page['style'], $ext) : css_node('style', $ext);
        ?>
        <?php if (ON_SERVER): ?>
        <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-34837342-1']);
        _gaq.push(['_trackPageview']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
        </script>
        <?php endif; ?>
    </head>
    <body>
        <div class="append_parent"><!-- append parent -->
        </div>
        <div class="header"><!-- head -->
            <?php if ($show_header) { include 'template/block/header.php'; } ?>
        </div>
        <div class="content">
            <?php include $template; ?>
        </div>
        <div class="footer"><!-- footer -->
            <?php if ($show_footer) { include 'template/block/footer.php'; } ?>
        </div>
        <?php
        echo js_node('jquery-1.8.3.min');
        echo js_node('bootstrap.min');
        echo js_var('G', array('ROOT_URL'=>ROOT));
        echo implode('', $page['scripts']);
        echo js_node('every');
        ?>
    </body>
</html>
