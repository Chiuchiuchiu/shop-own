/// <reference path="../../../../../../typings/tsd.d.ts" />

export class Tips{
    static target;
    public static error(msg){
        Tips.show(msg,'error');
    }
    public static success(msg){
        Tips.show(msg,'success');
    }
    public static warning(msg){
        Tips.show(msg,'warning');
    }
    public static show(msg,type,t=0){
        t = t>0?t:3500;
        type=type||'';

        let o = $('<div/>').addClass('tips '+type).html(msg);
        o.prependTo($(Tips.target));
        o.slideDown();
        setTimeout(function () {
            o.slideUp();
            setTimeout(function(){
                o.remove();
            },2000)
        },t);
    }
    public static clear(){
        $('>.tips',$(Tips.target)).remove();
    }
    public static confirm(){

    }
}