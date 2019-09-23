var provinces = new Array("京","沪","浙","苏","粤","鲁","晋","冀",
            "豫","川","渝","辽","吉","黑","皖","鄂",
            "津","贵","云","桂","琼","青","新","藏",
            "蒙","宁","甘","陕","闽","赣","湘");

var keyNums = new Array("0","1","2","3","4","5","6","7","8","9",
            "Q","W","E","R","T","Y","U","I","O","P",
            "A","S","D","F","G","H","J","K","L",
            "关闭","Z","X","C","V","B","N","M","删除");
var parkingNext=0;
	function showProvince(obj, parkP){
			$(obj).html("");
			var ss="";
			for(var i=0;i<provinces.length;i++){
				ss=ss+addKeyProvince(i, parkP)
			}
			$(obj).html("<ul class='park-pro'>"+ss+"<li class='li_close' onclick='closePro(\".park-keywords\");'><span>关闭</span></li><li class='li_clean' onclick='cleanPro();'><span>清空</span></li></ul>");
	}
	function showKeybord(obj){
			$(obj).html("");
			var sss="";
			for(var i=0;i<keyNums.length;i++){
				sss=sss+'<li class="ikey ikey'+i+' '+(i>9?"li_zm":"li_num")+' '+(i>28?"li_w":"")+'" ><span onclick="choosekey(this,'+i+');">'+keyNums[i]+'</span></li>'
			} 
			$(obj).html("<ul class='park-keyBord'>"+sss+"</ul>");
	}
    function addKeyProvince(provinceIds, parkP){
        var addHtml = '<li>';
            addHtml += '<span onclick="chooseProvince(this, \''+ parkP +'\');">'+provinces[provinceIds]+'</span>';
            addHtml += '</li>';
            return addHtml;
    }

    function chooseProvince(obj, parkP){
       $(parkP).text($(obj).text());
	   $(parkP).addClass("hasPro");
        parkingNext=0;
	   $(parkP).removeAttr('style');
	   $('.input-pn:eq('+ parkingNext + ')').attr('style', 'border: 1px solid #F8B500');
	   showKeybord('.park-keywords');
	}
	
	function choosekey(obj,jj){
		if(jj==29){
			$('.park-keywords').hide();
		}else if(jj==37){
		    var n = parkingNext;
			if($(".ppHas").length==0){
				$(".hasPro").text("");
				$(".hasPro").removeClass("hasPro");
                showProvince('.park-keywords', '#input-pn0');
                parkingNext=0;
			}

			$('.input-pn:eq('+parkingNext+')').text("");
			$('.input-pn:eq('+parkingNext+')').removeClass("ppHas");

            n = n -1;
            parkingNext=parkingNext-1;
			if(parkingNext<1){
				parkingNext=0;
            }

            if(n < 0){
                $('.input-pn').removeAttr('style');
                $('.input-pn:eq(0)').prev().attr('style', 'border: 1px solid #F8B500');
            }

			if(parkingNext === 0 && n < 0){
                $('.input-pn:eq('+ parkingNext + ')').siblings().removeAttr('style');
                $('.input-pn:eq('+ parkingNext + ')').prev().attr('style', 'border: 1px solid #F8B500');
            } else {
                $('.input-pn:eq('+ n + ')').siblings().removeAttr('style');
                $('.input-pn:eq('+ n + ')').attr('style', 'border: 1px solid #F8B500');
            }

		}else{
			if(parkingNext>6){
                $('.park-keywords').hide();
				return
			}

			for(var i = 0; i<$(".input-pn").length;i++){
				if(parkingNext==0 & jj<10 & $(".input-pn:eq("+parkingNext+")").hasClass("input-letter")){
                    $('.warning-content').text('车牌第二位为字母');
                    $('#warning-tips').show().fadeOut(2000);
					return;
				}
                $('.input-pn').removeAttr('style');
                $(".input-pn:eq("+ (parkingNext + 1) +")").attr('style', 'border: 1px solid #F8B500');
                $(obj).attr({style:"background-color: #F8B500;color: white"});
				$(".input-pn:eq("+parkingNext+")").text($(obj).text());
				$(".input-pn:eq("+parkingNext+")").addClass("ppHas");
				parkingNext=parkingNext+1;
				if(parkingNext>6){
					parkingNext=7;
				}
				getpai();
				return;
			}
			
		}
	}

	function closePro(obj) {
        $(obj).hide();
    }

	function cleanPro(){
       $(".input-pro,.input-pn").text("");
       $(".hasPro").removeClass("hasPro");
	   parkingNext=0;
	}
	function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
	function getpai(){
		var pai=trimStr($(".car_input").text());
		$(".btn-primary").attr("data-pai",pai);
	}

