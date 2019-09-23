<?php
/**
 * @var $id
 * @var \common\models\ArticleCategory $category
 * @var array $categoryList
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 * @var $status
 * @var bool|integer $hasHouse
 * @var string $projectName
 * @var string $flowStyleID
 */
$dataProvider->getModels();
?>
<style>
    .input-search {
        width: 75%;
        font-size: 14px;
        line-height: 1;
        text-align: right;
        padding: 0 .5em;
        box-shadow: none;
        height: 2.1em;
        border: 1px solid transparent;
        -webkit-transition: all .5s;
        transition: all .5s;
    }

    .button-search {
        width: 15%;
        height: 26px;
        background-color: #2196f3;
        border: none;
        color: white;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        border-radius: 5px;
    }

    .shoadowmy {
        width: 100%;
        height: 100%;
        position: fixed;
        background: rgba(200, 200, 200, .3);
        box-shadow: 0 0 0 100vmax rgba(200, 200, 200, .3);
        z-index: 8888;
        top: 0;
        left: 0;
    }

    /*---------*/
    .horizontal-ul {
        list-style: none;
        margin: 0px;
        padding: 0px;
    }

    .horizontal-ul li {
        float: left;
    }

    .horizontal-ul li span {
        color: #666;
    }

    .horizontal-menu-div {
        width: 100%;
        background-color: yellow;
        height: 47px;
    }

    .img-back {
        margin-left: 10px;
        margin-top: 5px;
    }

    /*---------*/

    .select-button {
        position: fixed;
        left: 0px;
        bottom: 0px;
        width: 98%;
        height: 43px;
        margin-bottom: 5px;
        background-color: red;
        z-index: 8888;
        color: white;
        font-size: 18px;
        border-radius: 5px;
        text-align: center;
        margin-left: 1%;
    }

    .select-div {
        position: fixed;
        left: 0px;
        bottom: 0px;
        width: 98%;
        height: 144px;
        margin-bottom: 55px;
        background-color: white;
        z-index: 9998;
        color: white;
        font-size: 18px;
        border-radius: 5px;
        text-align: center;
        margin-left: 1%;
    }

    .select-div .sel-li1 {
        clear: both;
        font-size: 14px;
        line-height: 35px;
        color: black;
    }

    .select-div .sel-li {
        clear: both;
        font-size: 18px;
        border-top: 1px solid #E5E5E5;
        line-height: 35px;
        color: royalblue;
    }

    .search-kw {
        width: 100%;
        height: 44px;
        z-index: 9999;
        top: 0;
        position: fixed;

    }

    .tab-list-sel {
        width: 100%;
        height: 40px;
        top: 0;
        position: fixed;

    }

    /*---------------------*/
</style>

<div class="panel" id="new-repair-list" data-max-page="<?= $dataProvider->pagination->pageCount ?>">
    <input type="hidden" id="type" value="<?= $type ?>">
    <div class="topPath horizontal-menu-div" style="background: #F8B708;width: 100%;top: 0;position: fixed;">

        <ul class="horizontal-ul">
            <li id="gohomeBtn" style="margin-top: 2px;">
                <img class="img-back" src="/static/images/leftback-icon.png">
            </li>
            <li style="width: 80%;text-align: center;">
                <span style="color: #ffffff; line-height: 33px;">寻物启事</span>
            </li>
            <li style="margin-top: 6px;">
                <a href="<?= $hasHouse ? '/search-notices/index' : 'javascript:void(0)'; ?>"
                   class="<?= $hasHouse ? '' : 'alterWin' ?>"><img src="/static/images/add-icon.png">
                </a>
            </li>
        </ul>
    </div>

    <!-----------搜索框------->
    <div id="shadowDiv" class="shoadowmy" style="display: none;">
        <div class="ui-row-flex tab-list search-kw " style="z-index:9999;background: #e2e2e2;margin: 47px 0 0 0">
            <div class="form-group">
                <input type="text" class="input-search" style="z-index: 99999;" id="kw" placeholder="输入关键词..."
                       data-required="">
                <button class="flow_style_w2 button-search" id="searchButton">搜索</button>
            </div>
        </div>
    </div>

    <!------------状态选择栏------------>
    <div id="shadowDivSel" class="shoadowmy" style="display:none;">
        <div class="select-div">
            <div class="sel-li1"><span>请选择</span></div>
            <div class="sel-li" id="selectAll" data-status="0"><span>全部</span></div>
            <div class="sel-li" id="selectUnReceive" data-status="1"><span>未领取</span></div>
            <div class="sel-li" id="selectReceive" data-status="2"><span>已领取</span></div>
        </div>
        <button class="select-button" id="cancelSelectBtn">取消</button>
    </div>

    <div class="ui-row-flex tab-list tab-list-sel" style="margin: 47px 0 0 0" id="navDiv">
        <div id="selectStatusBtn">
            <?php
            if ($status == 0) {
                $statusTip = "全部";
            } else if ($status == 1) {
                $statusTip = "未领取";
            } else if ($status == 2) {
                $statusTip = "已领取";
            }
            ?>
            <a href="javascript:;" <?= $type == '0' ? 'class="hover"' : '' ?> ><?= $statusTip ?></a>
        </div>
        <div>
            <a href="/search-notices/list?status=<?= $status ?>&type=1" <?= $type == '1' ? 'class="hover"' : '' ?> >我的发布</a>
        </div>
        <div>
            <a href="/search-notices/my-favorites?status=<?= $status ?>&type=2" <?= $type == '2' ? 'class="hover"' : '' ?> >我的收藏</a>
        </div>
        <div id="searchBtn">
            <a href="javascript:;"><img src="/static/images/search-icon.png"</a>
        </div>
    </div>

    <div style="margin-top: 87px">
        <?php
        if ($dataProvider->count == 0)
            echo "<div class=\"empty-status\"><i></i>暂无相关内容</div>";
        else
            echo $this->render('list-cell', ['dataProvider' => $dataProvider, 'type' => $type]);
        ?>
    </div>

    <div id="typeSelect">
        <div class="mask"></div>
        <div class="c-panel">
            <div class="t">
                <p>该功能仅对 <b><?= $projectName ?></b> 认证业主用户开放</p>
            </div>
            <div class="button-line">
                <button href="" class="btn-off btn-block btn-hidden">取消</button>
                <button class="placeholder"></button>
                <button data-go="/auth" class="btn-active btn-block">立即认证</button>
            </div>
        </div>
    </div>

    <div style="height: 6em;line-height: 6em;">

    </div>

</div>


<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    $('#new-repair-list').on('loaded', function () {
        app.scrollLoad($('#new-repair-list'), '/search-notices/list?status=<?= $status ?>&type=<?= $type ?>');

        $('.alterWin').bind('click', function () {
            $('#typeSelect').show();
        });

        $('.btn-hidden').bind('click', function () {
            $('#typeSelect').hide();
        });

    });

    //---------

    $('#gohomeBtn').on("click", function () {
        location.href = '/';
    });

    $('.receiveBtn').on("click", function () {
        var _id = $(this).attr("data-id");
        location.href = '/search-notices/receive?type=<?= $type ?>&id=' + _id;
    });

    $('#searchBtn').on("click", function () {
        $('#shadowDiv').show();
        $('#navDiv').hide();
        $('#kw').focus();
    });

    $('#searchButton').on('click', function () {
        var _kw = $("#kw").val();
        if (_kw != '') {
            location.href = '/search-notices/list?type=<?= $type ?>&kw=' + _kw.trim();
        } else {
            location.href = '/search-notices/list?type=<?= $type ?>';
        }
    });

    $('#selectStatusBtn').on('click', function () {
        var _type = $("#type").val();
        if (_type == 0) {
            $("#shadowDivSel").show();
        } else {
            location.href = '/search-notices/list';
        }
    });

    $('#cancelSelectBtn').on('click', function () {
        $("#shadowDivSel").hide();
    });

    $('#selectAll').on('click', function () {
        jumpList(0);
    });

    $('#selectUnReceive').on('click', function () {
        jumpList(1);
    });

    $('#selectReceive').on('click', function () {
        jumpList(2);
    });

    function jumpList(status) {
        location.href = '/search-notices/list?status=' + status;
    }


    var oDiv1 = document.getElementById('kw');		// 内层
    var oDiv2 = document.getElementById('shadowDiv');		// 外层
    var oEvent1 = null;
    var oEvent2 = null;
    var tmp = 0;
    oDiv1.onclick = function (ev){
        oEvent1 = ev || event;
        tmp = 1;
        oEvent1.preventDefault();
        return false;
    }

    oDiv2.onclick = function (ev){
        oEvent2 = ev || event;
        if(tmp != 1){
            $(this).hide();
            $("#navDiv").show();
            oEvent2.cancelBubble = true;
        }
    }
    //-----------
</script>

<?php \common\widgets\JavascriptBlock::end(); ?>

