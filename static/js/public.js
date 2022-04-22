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
        var loading, _opt = $.extend(opt, {
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                //错误方法增强处理
                if (!fn.error || fn.error(XMLHttpRequest, textStatus, errorThrown) !== false) {
                    $.popup.alert('对不起！服务器太忙了，请稍后再试！', 'error', 3);
                }
            },
            success: function (json, textStatus) {
                //成功回调方法增强处理
                if (fn.success && fn.success(json, textStatus, fn) === false) {
                    return;
                }
                if ((json.timeout === undefined || json.timeout <= 0) && _redirect(json)) {
                    return;
                }
                var callback = $.isFunction(fn.callback) ? fn.callback : function () {
                    _redirect(json);
                };
                if (json.redirect !== undefined && json.timeout > 0) {
                    json.message.info += '<i id="pop-timeout-redirect" class="fr">' + parseInt(json.timeout) + '</i>';
                    var xt = setInterval(function () {
                        var span = $('#pop-timeout-redirect'),
                                time = parseInt(span.text());
                        if (time < 1) {
                            clearInterval(xt);
                            _redirect(json);
                        } else {
                            span.text(--time);
                        }
                    }, 1000);
                }
                if (typeof json.status === 'string') {
                    if (typeof json.message.info === 'object') {
                        var info = [];
                        $.each(json.message.info, function (k, v) {
                            info.push(v);
                        });
                        json.message.info = '<p>' + info.join('</p><p>') + '</p>';
                    }
                    $.popup.alert(json.message.info, json.status).on('close', callback);
                }
            },
            beforeSend: function (XHR) {
                //提交前回调方法
                loading = $.popup.loading();
            },
            complete: function (XHR, TS) {
                //请求完成后回调函数 (请求成功或失败之后均调用)。
                loading.close();
            }
        });
        return $.ajax(_opt);
    };
    function _redirect(data) {
        if (data.redirect !== undefined) {
            if (data.redirect < 0) {
                if (data.redirect == -1) {
                    location.href = document.referrer || '/';
                } else if (history.length <= Math.abs(data.redirect)) {
                    location.href = '/';
                } else {
                    history.back(data.redirect);
                }
            } else {
                location.href = data.redirect;
            }
            return true;
        }
        return false;
    }
})(jQuery);
(function () {
    var popups = {}, index = 0, alertTypes = {
        success: 'success',
        primary: 'primary',
        secondary: 'secondary',
        error: 'danger',
        warn: 'warning',
        info: 'info',
        light: 'light',
        dark: 'dark'
    };
    $('body').on('click', 'div.modal', function (event) {
        if (event.target === this) {
            var modals = $('body>div.modal').addClass('modal-static overflow-hidden');
            setTimeout(function () {
                modals.removeClass('modal-static overflow-hidden');
            }, 200);
        }
    });
    function Popup() {
        if (!(this instanceof Popup)) {
            return Popup();
        }
        var _mask = $('<div class="modal-backdrop fade show"></div>'),
                _modal = $('<div class="modal fade"></div>'),
                _dialog = $('<div class="modal-dialog"></div>'),
                _content = $('<div class="modal-content"></div>');
        // 统一事件处理
        _modal.on('click', ':button', function () {
            var event = $(this).data('event');
            if (typeof event === 'string' && event.length > 0) {
                _modal.trigger(event, [this]);
            }
        });
        function close() {
            _modal.removeClass('show');
            setTimeout(function () {
                _modal.removeClass('d-block');
                $('body').removeClass('modal-open overflow-hidden');
                _mask.remove();
                _modal.remove();
            }, 200);
            delete popups[this.id];
        }
        function show() {
            $('body').append(_mask, _modal).addClass('modal-open overflow-hidden');
            _modal.addClass('d-block');
            setTimeout(function () {
                _modal.addClass('show');
            }, 200);
            popups[this.id] = this;
        }
        $.extend(this, {
            id: 'popup-' + (index++),
            close: function () {
                this.trigger('close');
                return this;
            },
            show: function () {
                this.trigger('show');
                return this;
            },
            append: function (html) {
                _content.append(html);
                return this;
            },
            on: function (type, callback) {
                _modal.on(type, callback);
                return this;
            },
            trigger: function (type, params) {
                _modal.trigger(type, params);
                return this;
            }
        });
        this.on('close', close);
        this.on('show', show);
        _modal.append(_dialog.append(_content));
    }
    $.extend(Popup, {
        // 弹出提示标语框
        alert: function (text, type, timeout, hiddenClose) {
            if (typeof type !== 'string' || alertTypes[type] !== undefined) {
                type = 'info';
            }
            var popup = new Popup(), _alert = $('<div class="alert my-0 alert-dismissible alert-' + alertTypes[type] + '" role="alert"></div>').append(text);
            if (typeof timeout === 'number' && timeout > 0) {
                var time = setInterval(function () {
                    timeout -= 1;
                    if (timeout < 0) {
                        clearInterval(time);
                        popup.close();
                    }
                }, 1000);
            }
            if (!hiddenClose) {
                _alert.append('<button type="button" class="btn-close" data-event="close"></button>');
            }
            popup.append(_alert);
            return popup.show();
        },
        // 弹出加载框
        loading: function () {
            return Popup.alert('<img src="/crbac/static/img/load.gif" width="25"/><span class="text-start text-info mx-2">正在加载中...</span>', 'info', 0, 1);
        },
        // 弹出标准展示框
        box: function (title, body, buttons) {
            var popup = new Popup(), _header = $('<div class="modal-header"></div>'), _body = $('<div class="modal-body"></div>'), _footer = $('<div class="modal-footer"></div>');
            if (title) {
                popup.append(_header.append(title, '<button type="button" class="btn-close" data-event="close"></button>'));
            }
            if (body) {
                popup.append(_body.append(body));
            }
            if (typeof buttons === 'object') {
                $.each(buttons, function (name, item) {
                    var auto = null;
                    switch (name) {
                        case 'close':
                            auto = {type: 'secondary', event: 'close', title: '关闭'};
                            break;
                        case 'cancel':
                            auto = {type: 'secondary', event: 'close', title: '取消'};
                            break;
                        case 'confirm':
                            auto = {type: 'secondary', event: 'confirm', title: '确定'};
                            break;
                    }
                    if (auto) {
                        var _type = typeof (item);
                        if (_type === 'string' && item.length > 0) {
                            item = $.extend(auto, {title: item});
                        } else if (_type === 'object') {
                            item = $.extend(auto, item);
                        } else {
                            item = auto;
                        }
                    }
                    if (typeof item === 'object') {
                        _footer.append('<button type="button" class="btn btn-' + item.type + '" data-event="' + item.event + '">' + item.title + '</button>');
                        if (item.event && typeof item.callback === 'function') {
                            popup.on(item.event, item.callback);
                        }
                    }
                });
                popup.append(_footer);
            }
            return popup.show();
        },
        // 关闭所有弹出的框
        closeAll: function () {
            for (var id in popups) {
                popups[id].close();
            }
        }
    });
    $.extend({popup: Popup}).fn.extend({
        ajaxPost: function (url, dataType, success, timeout) {
            return _ajax(_getUrl(url, this), this, dataType, 'post', success, timeout);
        },
        ajaxGet: function (url, dataType, success, timeout) {
            return _ajax(_getUrl(url, this), this, dataType, 'get', success, timeout);
        },
        ajaxJson: function (url, type, success, timeout) {
            return _ajax(_getUrl(url, this), this, 'json', type ? type : 'post', success, timeout);
        }
    });
    function _ajax(url, data, dataType, type, success, timeout) {
        if ($.isFunction(url)) {
            success = url, url = null;
        }
        var _data = {};
        if (data.is('form')) {
            if (!data.validate().form()) {
                return false;
            }
        }
        _data = data.serializeArray();
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
    }
    function _getUrl(url, elm) {
        return url ? url : elm.data('url');
    }
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
    $(':button.ajax-submit-data').click(function () {
        $('form').ajaxPost();
    });
})();