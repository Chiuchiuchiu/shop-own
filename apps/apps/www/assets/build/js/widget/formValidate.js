define(["require", "exports", "./tips"], function (require, exports, tips_1) {
    "use strict";
    var FormValidate = (function () {
        function FormValidate(obj) {
            this.form = null;
            this.isSuccess = true;
            this.form = obj;
        }
        FormValidate.prototype.run = function () {
            var that = this;
            $('input,select,textarea', this.form).each(function () {
                var label = $(this).attr('data-label');
                if (!label && $(this).next('label')) {
                    label = $(this).next('label').text();
                }
                if ($(this).attr('data-required') != undefined && $(this).val() == '') {
                    that.errorHandle($(this), label + " 不能为空");
                }
            });
            return this.isSuccess;
        };
        FormValidate.prototype.errorHandle = function (obj, msg) {
            this.isSuccess = false;
            obj.addClass('has-error');
            tips_1.Tips.error(msg);
        };
        return FormValidate;
    }());
    exports.FormValidate = FormValidate;
});
