/// <reference path="../../../../../../typings/tsd.d.ts" />
import {Tips} from "./tips";

export class FormValidate{
    private form=null;
    private isSuccess=true;
    constructor(obj){
        this.form = obj;
    }
    public run(){
        let that = this;
        $('input,select,textarea',this.form).each(function(){
            let label = $(this).attr('data-label');
            if(!label && $(this).next('label')){
                label = $(this).next('label').text();
            }
            if($(this).attr('data-required')!= undefined && $(this).val()==''){
                that.errorHandle($(this),label+" 不能为空");
            }
        });
        return this.isSuccess;
    }

    public errorHandle(obj,msg){
        this.isSuccess=false;
        obj.addClass('has-error');
        Tips.error(msg);
    }
}