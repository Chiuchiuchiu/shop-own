var app = function(){
};
app.prototype.tips=function(msg,time){
    time = time || 3000;
    var c=$('<div class="ui-poptips ui-poptips-info">'+
        '<div class="ui-poptips-cnt"><i></i>'+msg+'</div>'+
        '</div>');
    c.appendTo($('body'));
    setTimeout(function(){
        c.remove();
    },time);
};
app = new app();
