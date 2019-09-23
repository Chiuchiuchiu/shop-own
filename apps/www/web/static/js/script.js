$(function(){ 
/*tab1*/	
$('[data-widget="tab-ground"]').each(function(index, element) {
	var $this = $(this);
    $this.find('[data-widget="tab-item"]').click(function(e){
		$this.toggleClass('on');
//		$this.find('[data-widget="tab-content"]').toggle();
	});
});
//弹窗
function popupOpen(){
	$('#popup').show();
	}
function popupClose(){
	$('#popup').hide();
	}
//点击显示弹窗
$('[data-popup="open"]').click(function(e) {
    popupOpen();
});
//点击关闭弹窗
$('[data-popup="close"]').click(function(e) {
    popupClose();
});
	//radio
		$('[radiobox]').click(function(){
			var input = $(this).find('input');
				$(this).find('i').addClass("checked");
				input.attr('checked',true);
				$(this).siblings().find('i').removeClass("checked");
				$(this).siblings().find('input').attr('checked',false);
		});
		//模拟模拟checkbox 
		$('[checkbox]').click(function(){
			var input = $(this).find('input');
			if(input.attr('checked')){
				$(this).find('i').removeClass("checked");
				input.attr('checked',false);
			}else{
				$(this).find('i').addClass("checked");
				input.attr('checked',true);
			};
		});
		$('[checkboxAll]').click(function(){
			if($(this).find('i').hasClass('checked')){
				$(this).find('i').removeClass("checked");
				$('[checkbox]').each(function(index, el) {
					var input = $(this).find('input');
					$(this).find('i').removeClass("checked");
					input.attr('checked',false);
				});
			}else{
				$(this).find('i').addClass("checked");
				$('[checkbox]').each(function(index, el) {
					var input = $(this).find('input');
					$(this).find('i').addClass("checked");
					input.attr('checked',true);
				});
			};
		})	
})


