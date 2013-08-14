
$(function () {
    // 角色输入框
    var okBtn = $('.search-add-role-form input[type=submit]');
    var input = $('input.typeahead');
    input.typeahead({
        source: function (query, process) {
            $.get(G.ROOT_URL+'role', {keyword:query}, function (ret) {
                r = $.map(ret, function (e) { return e.name; });
                process(r);
                for (var i = 0; i < r.length; i++) {
                    if (r[i] == query) {
                        okBtn.attr('value', '查看 '+query).show();
                        break;
                    }
                    
                };
            }, 'json');
            okBtn.attr('value', '创建 '+query).show();
        },
        updater: function (query) {
            okBtn.attr('value', '查看 '+query).show();
            return query;
        }
    });
});