<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/12
 * Time: 16:49
 */
/* @var array $model */

?>
<div id="past-winners">

    <div  id="focus_Box">
        <span class="prev">&nbsp;</span>
        <span class="next">&nbsp;</span>
        <ul>

            <?php foreach ($model as $key => $row){?>
                <?php /* @var $row \common\models\ButlerElectionActivity */ ?>

                <li >
                    <a href="#">
                        <img  alt="<?= $row->name ?>" src="<?= Yii::getAlias($row->head_img) ?>" />
                    </a>

                    <div class="absolute_p">
                        <p>
                            <?= $row->name ?>
                        </p>
                        <p>
                            <?= $row->group == \common\models\ButlerElectionActivity::GROUP_BUTLER ? '管家' : '保安'; ?>
                        </p>
                        <p>
                            <?= $row->project->house_name ?>
                        </p>
                    </div>

                </li>

            <?php } ?>

        </ul>
    </div>

</div>


<?php \common\widgets\JavascriptBlock::begin(); ?>
    <script type="text/javascript">
        (function(){
            var html = document.documentElement;
            var hWidth = html.getBoundingClientRect().width;
            html.style.fontSize = hWidth/15 + "px";
        })();

        function move(){
            var y=0;
            var json = [
                {zIndex:1,width:6+'rem', height:4+'rem', top:1+'rem', left:0+'rem'},
                {zIndex:2,width:8+'rem', height:6+'rem', top:0+'rem', left:1+'rem'},
                {zIndex:3,width:9+'rem', height:7+'rem', top:-0.5+'rem', left:3+'rem'},
                {zIndex:2,width:8+'rem', height:6+'rem', top:0+'rem', left:6+'rem'},
                {zIndex:1,width:6+'rem', height:4+'rem', top:1+'rem', left:12+'rem'},
            ];
            for(var i=0,l=json.length;i<l;i++){
                for(var key in json[i]){
                    $("li").eq(i).css(key,json[i][key]);
                }
            }
            $(".absolute_p").eq(2).css("display","block");
            function absolute_p(){
                $(".absolute_p").css("display","none");
                if(y>=-2){
                    $(".absolute_p").eq(y+2).css("display","block");
                }
                else{
                    $(".absolute_p").eq(y+$("li").length+2).css("display","block");
                }
            }
            move.prototype.prev=function(){
                y--;
                for(var i=0,l=json.length;i<l;i++){
                    if(y<-($("li").length)){
                        y+=$("li").length;
                    }
                    $("li").eq(i+y).animate(json[i]);
                }
                absolute_p();
            };
            move.prototype.next=function(){
                y++;
                if(y>0){
                    y-=$("li").length;
                }
                for(var i=0,l=json.length;i<l;i++){
                    /* alert(key+':'+json[i][key]);*/
                    $("li").eq(i+y).animate(json[i]);
                }
                absolute_p();
            }
        }
        var move=new move();
        var timer=null;
        timer=setInterval(move.next,2000);
        $(".prev").on("click",function(){
            move.prev();
            clearInterval(timer);
            timer=setInterval(move.prev,2000);
        });
        $(".next").on("click",function(){
            move.next();
            clearInterval(timer);
            timer=setInterval(move.next,2000);
        });

    </script>
<?php \common\widgets\JavascriptBlock::end(); ?>