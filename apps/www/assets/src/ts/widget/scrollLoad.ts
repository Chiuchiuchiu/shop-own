/// <reference path="../../../../../../typings/tsd.d.ts" />

export class scrollLoad {
    private isLoading = false;
    private page = 1;
    private range = 1;
    private maxPage = 1;

    constructor(target, url, page, successFunc, range, main) {
        this.range = range || 150;
        // main = main || $('#project-window');
        main = main || $(document);
        this.page = page || 1;
        this.maxPage = parseInt($(target).attr('data-max-page')+'');
        successFunc = typeof successFunc == "function" ? successFunc : function (res) {
            $(target).append(res);
            $(target).trigger('loadedPage');
            this.page++;
            this.isLoading = false;
        };
        this.page++;
        $(main).on('scroll', function () {
            let pos = $(main).scrollTop();
            let range = $(target).height() - $('#project-window').height()- pos;
            if (range < this.range && !this.isLoading && this.page<=this.maxPage) {
                this.isLoading = true;
                let _url = url + (url.indexOf('?') > 0 ? '&' : '?') + 'page=' + this.page;
                $.get(_url, successFunc.bind(this))
            }
        }.bind(this))
    }
}