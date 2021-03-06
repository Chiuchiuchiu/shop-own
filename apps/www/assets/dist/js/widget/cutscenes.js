/// <reference path="../../../../../../typings/tsd.d.ts" />
define(["require", "exports"], function (require, exports) {
    "use strict";
    var Cutscenes = (function () {
        function Cutscenes(main) {
            this.main = null;
            this.main = main;
            history.pushState({
                url: window.location.href,
                title: document.title,
                html: $('#project-main', main).html()
            }, document.title + '', window.location.href + '');
        }
        Cutscenes.prototype.init = function () {
            this.bind();
            var main = this.main;
            var that = this;
            window.addEventListener("popstate", function (event) {
                $('.panel', main).trigger('unload');
                if (Cutscenes.prevContent == null)
                    return;
                else if (event && event.state) {
                    document.title = event.state.title;
                    $('#project-main', main).html(event.state.html);
                }
                else {
                    document.title = Cutscenes.currentState.title;
                    $('#project-main', main).html(Cutscenes.currentState.html);
                }
                $('.panel', main).trigger('loaded');
                $('body').addClass('loaded').removeClass('loading');
            });
            $.ajaxSetup({
                beforeSend: function () {
                    $('#ui-loading').show();
                },
                complete: function (JQueryXHR, textStatus) {
                    $('#ui-loading').hide();
                    if (JQueryXHR.responseText && JQueryXHR.responseText.substr(0, 1) == '{' && !JQueryXHR.responseJSON) {
                        JQueryXHR.responseJSON = $.parseJSON(JQueryXHR.responseText);
                    }
                    if (JQueryXHR.responseJSON && (JQueryXHR.responseJSON.code == 302 || JQueryXHR.responseJSON.code == 301)) {
                        if (/^https?:\/\//.test(JQueryXHR.responseJSON.url)) {
                            window.location.href = JQueryXHR.responseJSON.url;
                        }
                        else
                            that.go(JQueryXHR.responseJSON.url);
                        return;
                    }
                },
                error: function () {
                    if (window.confirm('抱歉,出错了。是否尝试重新加载?')) {
                        window.location.href = window.location.href;
                    }
                }
            });
            $('.panel', main).trigger('loaded');
            $('body').trigger('loaded');
        };
        Cutscenes.prototype.bind = function () {
            var that = this;
            $(this.main).on('click', 'a', function (e) {
                if ($(this).attr('data-origin')) {
                    return true;
                }
                if ($(this).is('[href^=tel:]')) {
                    return true;
                }
                e.preventDefault();
                var url = $(this).attr('href');
                if (url && url.substr(0, 11).toLowerCase() != 'javascript:') {
                    that.go(url);
                }
            });
            $(this.main).on('click', '[data-go]', function (e) {
                e.preventDefault();
                var url = $(this).attr('data-go');
                that.go(url);
            });
        };
        Cutscenes.prototype.unbind = function () {
            $('a', this.main).off('tap click');
        };
        Cutscenes.prototype.onload = function () {
        };
        Cutscenes.prototype.go = function (url) {
            var t = new Date();
            var main = this.main;
            Cutscenes.currentState = {
                url: document.location.href,
                title: document.title,
                html: $('#project-main', main).html()
            };
            $.ajax({
                url: url,
                beforeSend: function () {
                    t = new Date();
                    $('body').removeClass('loaded loading load-complete');
                    setTimeout(function () {
                        $('body').addClass('loading').trigger('loading');
                    }, 15);
                    $('.panel', main).trigger('unload');
                    $('#project-out-view', main).html($('#project-main', main).html());
                    $('#project-main *,#project-out-view *', main).off();
                },
                success: function (res) {
                    Cutscenes.prevContent = res;
                    if (res && res.length > 0 && res.indexOf('project-main') > 10) {
                        res = $(res).find('div>#project-main').html();
                        setTimeout(function () {
                            $('#project-out-view', main).html('');
                            $('#project-main', main).html(res);
                            $('#project-main', main).scrollTop(0);
                            history.pushState({
                                url: url,
                                title: document.title,
                                html: res
                            }, document.title + '', url + '');
                            $('body').addClass('loaded').removeClass('loading').trigger('loaded');
                            setTimeout(function () {
                                $('.panel', main).trigger('loaded');
                                $('body').addClass('load-complete');
                            }, 650);
                        }, 1100 - ((new Date()).getTime() - t.getTime()));
                    }
                }
            });
        };
        Cutscenes.prevContent = null;
        Cutscenes.currentState = null;
        return Cutscenes;
    }());
    exports.Cutscenes = Cutscenes;
});
