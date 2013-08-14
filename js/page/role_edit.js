$(function () {
    $('#avatar_upload').fileupload({
        dataType: 'json',
        start: function () {
            var upload = $('label[for=avatar_upload]')
                .text('载入中...').prop('disabled', true);
        },
        done: function (e, data) {
            $('.info img').attr('src', data.result.path);
            $.post(
                '/role_edit',
                {
                    id: $('.role-info').data('id'),
                    field: 'avatar',
                    value: data.result.path
                },
                function (ret) {
                    $('label[for=avatar_upload]').text('更换头像').prop('disabled', false);
                }
            );
        }
    });
    $('input[name=is_v]').change(function () {
        $.post(
            '/role_edit',
            {
                id: $('.role-info').data('id'),
                field: 'is_v',
                value: $(this).prop('checked') ? 1 : 0
            },
            function (ret) {
            }
        );
    });
});