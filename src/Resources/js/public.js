(function ($) {
    //备份jquery的ajax方法
    //重写jquery的ajax方法
    $._ajax = function (opt) {
        if (!opt.dataType || !/^json$/i.test(opt.dataType)) {
            return $.ajax(opt);
        }
        //备份opt中error和success方法
        var fn = {};
        if (opt.error && $.isFunction(opt.error)) {
            fn.error = opt.error;
        }
        if (opt.success && $.isFunction(opt.success)) {
            fn.success = opt.success;
        }
        //扩展增强处理
        var _opt = $.extend(opt, {
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                //错误方法增强处理
                if (!fn.error || fn.error(XMLHttpRequest, textStatus, errorThrown) !== false) {
                    alert('对不起！服务器太忙了，请稍后再试！');
                }
            },
            success: function (json, textStatus) {
                //成功回调方法增强处理
                if (fn.success && fn.success(json, textStatus, fn) === false) {
                    return;
                }
                var callback = $.isFunction(fn.callback) ? fn.callback : function () {
                    if (json.redirect !== undefined && json.timeout > 0) {
                        location.href = json.redirect;
                    }
                };
                if (json.redirect !== undefined && json.timeout > 0) {
                    json.message.info += '<i id="pop-timeout-redirect" class="fr">' + parseInt(json.timeout) + '</i>';
                    var xt = setInterval(function () {
                        var span = $('#pop-timeout-redirect'),
                                time = parseInt(span.text());
                        if (time < 1) {
                            clearInterval(xt);
                            location.href = json.redirect;
                        } else {
                            span.text(--time);
                        }
                    }, 1000);
                }
                //登录失效
                if (json.status === 'logon_failure') {
                    json.redirect = json.redirect ? json.redirect : '/login.html';
                    $.showWarn(json.message.info, json.message.title, callback);
                } else if (json.status === 'success') {
                    $.showSuccess(json.message.info, json.message.title, callback);
                } else if (json.status === 'error') {
                    if (typeof json.message.info === 'object') {
                        var info = [];
                        $.each(json.message.info, function (k, v) {
                            info.push(v);
                        });
                        json.message.info = '<p>' + info.join('</p><p>') + '</p>';
                    }
                    $.showError(json.message.info, json.message.title, callback);
                }
                if (json.redirect !== undefined && (json.timeout === undefined || json.timeout < 0)) {
                    location.href = json.redirect;
                }
            },
            beforeSend: function (XHR) {
                //提交前回调方法
                $.showLoad();
            },
            complete: function (XHR, TS) {
                //请求完成后回调函数 (请求成功或失败之后均调用)。
                $.hiddenLoad();
            }
        });
        return $.ajax(_opt);
    };
})(jQuery);
(function () {
    var mask = !1,
            popup = !1,
            load = !1;
    $.extend({
        showMask: function () {
            if (!mask) {
                mask = $('<div class="public-mask"></div>');
                $('body').append(mask);
            } else {
                mask.show();
            }
            return this;
        },
        hiddenMask: function () {
            if (mask && (!popup || popup.is(':hidden')) && (!load || load.is(':hidden'))) {
                mask.hide();
            }
            return this;
        },
        showLoad: function () {
            this.showMask();
            if (!load) {
                load = $('<img class="public-load" src="/img/load.gif"/>');
                $('body').append(load);
            } else {
                load.show();
            }
        },
        hiddenLoad: function () {
            if (load) {
                load.hide();
                this.hiddenMask();
            }

        },
        showPopup: function (contents, title, button, callback, icon, close) {
            //显示层
            this.showMask();
            if (!popup) {
                popup = $('<div class="popbox"></div>');
                $('body').append(popup);
            } else {
                //有就清空内容
                popup.empty().show();
            }
            //显示内容
            var cont = $('<div class="pop-content rel zoom"><div>');
            //标题
            title = title === undefined ? '提醒一下' : title;
            close = close === undefined || close ? $('<i>X</i>') : '';
            title = $('<p class="pop-tit">' + title + '</p>');
            popup.append(title), close && title.append(close) && close.click(function () {
                hide('close');
            });
            //图标
            if (icon !== undefined && icon) {
                cont.append('<i class="' + icon + ' fl"></i>');
            }
            //按钮事件处理
            if (button !== undefined && button) {
                var but = $('<a href="javascript:void(0);" class="surebtn">' + button + '</a>').click(function () {
                    hide('button');
                });
                cont.append(but);
            }
            if (but || close) {
                var hide = function (a) {
                    if (typeof (callback) !== 'function' || callback(popup, a) !== false) {
                        $.hiddenPopup();
                    }
                }
            }
            popup.append(cont.append('<div>' + contents + '</div>'));
            return this;
        },
        hiddenPopup: function () {
            if (popup) {
                popup.empty().hide();
                this.hiddenMask();
            }
            return this;
        },
        showSuccess: function (contents, title, callback, button) {
            button = button === undefined ? '确定' : button;
            this.showPopup(contents, title, button, callback, 'pop-success', false);
        },
        showError: function (contents, title, callback, button) {
            button = button === undefined ? '确定' : button;
            this.showPopup(contents, title, button, callback, 'pop-error', false);
        },
        showWarn: function (contents, title, callback, button) {
            button = button === undefined ? '确定' : button;
            this.showPopup(contents, title, button, callback, 'pop-plaint', false);
        }
    }).fn.extend({
        ajaxPost: function (url, dataType, success, timeout) {
            return ajax(getUrl(url, this), this, dataType, 'post', success, timeout);
        },
        ajaxGet: function (url, dataType, success, timeout) {
            return ajax(getUrl(url, this), this, dataType, 'get', success, timeout);
        },
        ajaxJson: function (url, type, success, timeout) {
            return ajax(getUrl(url, this), this, 'json', type ? type : 'post', success, timeout);
        }
    });
    var ajax = function (url, data, dataType, type, success, timeout) {
        if ($.isFunction(url)) {
            success = url, url = null;
        }
        var _data = {};
        if (data.is('form')) {
            if (!data.validate().form()) {
                return false;
            }
            _data = data.serialize();
        } else {
            data.find(':enabled').each(function () {
                this.name && (_data[this.name] = $.trim(this.value));
            });
        }
        timeout = timeout === undefined || !$.isNumeric(timeout) ? 10000 : parseInt(timeout);
        dataType = dataType === undefined ? 'json' : dataType;
        return $._ajax({
            url: url,
            data: _data,
            dataType: dataType,
            type: type,
            timeout: timeout,
            success: success
        });
    },
            getUrl = function (url, elm) {
                return url ? url : elm.data('url');
            };
})();
(function () {
    $.extend($.validator.messages, {
        required: "此字段必填.",
        remote: "已经占用.",
        email: "请输入有效的电子邮件地址.",
        url: "请输入一个有效的网址.",
        date: "请输入有效的日期.",
        dateISO: "请输入有效的日期（ISO）.",
        number: "请输入有效数字.",
        digits: "请输入数字.",
        creditcard: "请输入有效的信用卡号码.",
        equalTo: "请再次输入相同的值.",
        accept: "请输入一个有效的扩展名.",
        maxlength: $.validator.format("请输入不超过 {0} 字符."),
        minlength: $.validator.format("请输入至少 {0} 字符."),
        rangelength: $.validator.format("请输入一个 {0} 到 {1} 长的字符."),
        range: $.validator.format("请输入一个值 {0} 和 {1} 之间."),
        max: $.validator.format("请输入一个值小于或等于 {0}."),
        min: $.validator.format("请输入一个值大于或等于 {0}.")
    });
    $('form').validate();
    $('input.ajax-submit-data').click(function () {
        $('form').ajaxPost();
    });
})();