/// <reference path="../../../../../typings/tsd.d.ts" />

import {Cutscenes} from "./widget/cutscenes";
import {Tips} from "./widget/tips";
import {FormValidate} from "./widget/formValidate";
import {scrollLoad} from "./widget/scrollLoad";
// interface Window {app:APP}
interface Window {app:APP,location}
declare var window: Window;

class APP{
    private cutscenes:Cutscenes;
    public constructor(){
        this.cutscenes = new Cutscenes($('main'));
        Tips.target = '#tips-box';
        $('body').on('loading',function () {
            Tips.clear();
        });
        $('input,select,textarea').bind('focus',function(){
            $(this).removeClass('has-error');
        });
    }
    public init(){
        // this.cutscenes.init();
        $('[data-go]').on('click',function (e) {
            e.preventDefault();
            const url = $(this).attr('data-go');
            window.location.href = url;
        });
        $('.panel').trigger('loaded');
    }
    public tips(){
        return Tips;
    }

    public showLoading(){
        $('#ui-loading').show();
    }
    public hideLoading(){
        $('#ui-loading').hide();
    }

    public formValidate(obj){
        var valid = new FormValidate(obj);
        return valid.run();
    }
    public scrollLoad(target,url,page,successFunc,range,main){
        return new scrollLoad(target,url,page,successFunc,range,main);
    }
    public go(url){
        // this.cutscenes.go(url);
        window.location.href = url;
    }
}
window.app = new APP();
window.app.init();