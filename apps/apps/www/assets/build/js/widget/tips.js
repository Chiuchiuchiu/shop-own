/// <reference path="../../../../../../typings/tsd.d.ts" />
define(["require", "exports"], function (require, exports) {
    "use strict";
    var Tips = (function () {
        function Tips() {
        }
        Tips.error = function (msg) {
            Tips.show(msg, 'error');
        };
        Tips.success = function (msg) {
            Tips.show(msg, 'success');
        };
        Tips.warning = function (msg) {
            Tips.show(msg, 'warning');
        };
        Tips.show = function (msg, type, t) {
            if (t === void 0) { t = 0; }
            t = t > 0 ? t : 3500;
            type = type || '';
            var o = $('<div/>').addClass('tips ' + type).html(msg);
            o.prependTo($(Tips.target));
            o.slideDown();
            setTimeout(function () {
                o.slideUp();
                setTimeout(function () {
                    o.remove();
                }, 2000);
            }, t);
        };
        Tips.clear = function () {
            $('>.tips', $(Tips.target)).remove();
        };
        return Tips;
    }());
    exports.Tips = Tips;
});
