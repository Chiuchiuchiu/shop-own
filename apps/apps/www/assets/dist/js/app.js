/// <reference path="../../../../../typings/tsd.d.ts" />
define(["require", "exports", "./widget/cutscenes", "./widget/tips", "./widget/formValidate", "./widget/scrollLoad"], function (require, exports, cutscenes_1, tips_1, formValidate_1, scrollLoad_1) {
    "use strict";
    exports.__esModule = true;
    var APP = (function () {
        function APP() {
            this.cutscenes = new cutscenes_1.Cutscenes($('main'));
            tips_1.Tips.target = '#tips-box';
            $('body').on('loading', function () {
                tips_1.Tips.clear();
            });
            $('input,select,textarea').bind('focus', function () {
                $(this).removeClass('has-error');
            });
        }
        APP.prototype.init = function () {
            // this.cutscenes.init();
            $('[data-go]').on('click', function (e) {
                e.preventDefault();
                var url = $(this).attr('data-go');
                window.location.href = url;
            });
            $('.panel').trigger('loaded');
        };
        APP.prototype.tips = function () {
            return tips_1.Tips;
        };
        APP.prototype.showLoading = function () {
            $('#ui-loading').show();
        };
        APP.prototype.hideLoading = function () {
            $('#ui-loading').hide();
        };
        APP.prototype.formValidate = function (obj) {
            var valid = new formValidate_1.FormValidate(obj);
            return valid.run();
        };
        APP.prototype.scrollLoad = function (target, url, page, successFunc, range, main) {
            return new scrollLoad_1.scrollLoad(target, url, page, successFunc, range, main);
        };
        APP.prototype.go = function (url) {
            // this.cutscenes.go(url);
            window.location.href = url;
        };
        return APP;
    }());
    window.app = new APP();
    window.app.init();
});
