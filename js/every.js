/* 
 * by xc
 */

$(function () {
    $('h1 span.text').naughtyText({range: 12});
    
    // 评论
    $('.comment-form').each(function () {
        var commentForm = $(this);
        var okBtn = commentForm.find('.comment-btn');
        commentForm.find('textarea').focus(function () {
            commentForm.addClass('on').removeClass('shrink');
        }).focusout(function () {
            if ($(this).val() === '') {
                commentForm.removeClass('on').addClass('shrink');
            }
        }).keyup(function () {
            if ($(this).val() == '') {
                okBtn.prop('disabled', true);
            } else {
                okBtn.prop('disabled', false);
            }
        });
    });
});

