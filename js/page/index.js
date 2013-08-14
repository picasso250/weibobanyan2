$(function () {
    var linkize = function (str) {
        return str.
            replace(/\[(@([^@]+?))\sv\]/g, '<a href="'+G.ROOT_URL+'role?name='+'$2'+'">$1<span class="verify">V</span></a>').
            replace(/\[(@(.+?))\]/g, '<a href="'+G.ROOT_URL+'role?name='+'$2'+'">$1</a>'); // how ?? 我不知道
    };
    $('.text').each(function () {
        $(this).html(linkize($(this).html()));
    });

    var updateComment = function (anythingInCommentDiv) {
        var parent = anythingInCommentDiv.parents('li.twit');
        $.get(G.ROOT_URL+'twit/'+parent.attr('data-id'), {
            method: 'comment'
        }, function (data) {
            parent.find('ul.comment').html(data).show('fast');
            parent.find('div.comment').show();//.find('textarea').focus();
        }, 'html');
    };

    // 评论
    $('a.comment-btn').click(function () {
        updateComment($(this));
        return false;
    });
    $('form.post-comment').each(function () {
        // disable a button
        var that = $(this);
        that.ajaxForm(function () {
            updateComment(that);
            that.find('.comment-form').removeClass('on').
                find('textarea').val('').focusout();
        });
    });

    // 转发
    $('a.retweet-btn').click(function () {
        var li = $(this).parents('li.twit');
        li.find('div.retweet').show().find('textarea').focus();
        li.find('div.comment').hide();
        return false;
    });

    var dateFormat = function (date) {
        return [
            [date.getFullYear(), date.getMonth()+1, date.getDate()].join('-'),
            [date.getHours(), date.getMinutes(), date.getSeconds()].join(':')
        ].join(' ');
    };

    // 所有的转发框和评论框都有at功能
    // $('.post-comment-form textarea, .retweet-form textarea, .post-form textarea').atBox();

    $('a.open-remind').click(function () {
        $('ul.remind').toggle('fast');
        return false;
    });

    $('.post a.know').click(function () {
        $(this).parent().hide('fast');
        $.post(G.ROOT_URL+'post', {method: 'know'}, function () {
            console.log('ok');
        });
    });

    $('a.up-btn').click(function () {
        $(this).hide();
        $.get($(this).attr('href'), {}, function () {
        });
        return false;
    });


    var interval = 1000 * 3;
    var getNotification = function () {
        console.log('getNotification');
        $.get(
            '/ajax',
            {
                action: 'get',
                target: 'notification'
            },
            function (ret) {
                console.log(ret);
                if (ret) {
                    $('span.name').tooltip({html: ret+' message comes'})
                };
                setTimeout(getNotification, interval);
            },
            'html');
    };
    // setTimeout(getNotification, interval);
    
});

$(function () {
    $('.image-holder').hide();
    $('#fileInput').fileupload({
        dataType: 'json',
        start: function () {
            $('label[for=fileInput]')
                .text('上传...')
                .prop('disabled', true);
        },
        done: function (e, data) {
            $('input[name=image_src]').val(data.result.path);
            var img = $('<img />').attr('src', data.result.path).css('class', 'img-polaroid');
            $('.image-holder').append(img).show('fast');
            $('label[for=fileInput]')
                .text('图片')
                .prop('disabled', false);
        }
    });
});