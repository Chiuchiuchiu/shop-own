/// <reference path="../../../../../../typings/tsd.d.ts" />
define(["require", "exports"], function (require, exports) {
    "use strict";
    var scrollLoad = (function () {
        function scrollLoad(target, url, page, successFunc, range, main) {
            this.isLoading = false;
            this.page = 1;
            this.range = 1;
            this.maxPage = 1;
            this.range = range || 150;
            // main = main || $('#project-window');
            main = main || $(document);
            this.page = page || 1;
            this.maxPage = parseInt($(target).attr('data-max-page') + '');
            successFunc = typeof successFunc == "function" ? successFunc : function (res) {
                $(target).append(res);
                $(target).trigger('loadedPage');
                this.page++;
                this.isLoading = false;
            };
            this.page++;
            $(main).on('scroll', function () {
                var pos = $(main).scrollTop();
                var range = $(target).height() - $('#project-window').height() - pos;
                if (range < this.range && !this.isLoading && this.page <= this.maxPage) {
                    this.isLoading = true;
                    var _url = url + (url.indexOf('?') > 0 ? '&' : '?') + 'page=' + this.page;
                    $.get(_url, successFunc.bind(this));
                }
            }.bind(this));
        }
        return scrollLoad;
    }());
    exports.scrollLoad = scrollLoad;
});
