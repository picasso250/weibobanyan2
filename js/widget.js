// 各种小控件

(function ($, window) {

    // 让文本框可以@别人
    $.fn.atBox = function () {
        return $(this).each(function () {
            var main = $(this);
            var panel = $('<div class="pop-panel"><span>敲一下空格表示你写完了</span><div>').hide();
            var ul = $('<ul></ul>');
            main.after(panel.append(ul));
            main.keyup(function (e) {
                var text = main.val();
                if (/@.*\s+$/.test(text)) {
                    console.log('???');
                    panel.hide();
                    return;
                }
                var reg = /@[^\s]*$/; // 要不要指定不要包含@呢？头疼
                if (reg.test(text)) {
                    var atname = text.match(reg)[0];
                    var name = atname.replace(/(@)(.*)/, '$2');
                    $.get(G.ROOT_URL+'role', {
                        keyword: name
                    }, function (ret) {
                        ul.html($.map(ret, function (e) {
                            return '<li>'+'<span class="name">'+e.name+'</span>'+(e.is_v==1?'<span class="verify">V</span>':'')+'</li>'
                        }).join(''));
                        panel.show().find('li').click(function () {
                            var name = $(this).find('.name').text()+' ';
                            main.val(main.val().replace(reg, '@'+name)).focus();
                            panel.hide();
                        });
                    }, 'json');
                }
            });
        });
    };

    $.fn.keySelect = function() {
        var main = $(this);
        var pos = 0;
        var lis = null;
        var update = function () {
            lis = main.find('li').removeClass('on');
            var li = lis.eq(pos);
            if (li[0]) {
                li.addClass('on');
            } else {
                pos = 0;
            }
        };
        main.up = function () {
            pos--;
            update();
        };
        main.down = function () {
            pos++;
            update();
        };
        main.reset = function () {
            pos = 0;
            update();
        };
        main.currentNode = function () {
            return lis.eq(pos);
        };
        return main;
    };
    
    // 字歪歪斜斜
    $.fn.naughtyText = function (para) {
        
        // 处理参数
        if (para == null) {
            para = {};
        }
        if (!$.isPlainObject(para)) {
            $.error('not good para');
        }
        para = $.extend({
            range: 20 // measure by degree
        }, para);
        
        return $(this).each(function () {
            var that = $(this);
            
            // 拓展之
            that.html($.map(that.text(), function (t) {
                return '<span>' + t + '</span>';
            }).join(''));
            
            $.fn.wmCss = function (name, value) {
                var webkit = '-webkit-' + name;
                var moz = '-moz-' + name;
                return $(this).css(webkit, value).css(moz, value);
            };
            var shake = function(obj, range) {
                var deg = Math.round(Math.random() * range * 2 - range);
                obj.wmCss('transform', 'rotate(' + deg + 'deg)');
            };
            
            // 加样式
            var range = para.range;
            that.find('span').css('display', 'inline-block').each(function () {
                shake($(this), range);
            });
        });
    };
})(jQuery, window);