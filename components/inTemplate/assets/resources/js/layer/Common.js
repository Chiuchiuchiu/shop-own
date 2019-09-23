
function ShowBuQrcode(str,id) {
    layer.open({
        type: 2,
        title: str,
        shadeClose: true,
        shade: 0.8,
        area : ['500px' , '500px'],
        content: '/butler/show-qrcode/' + id + '?SysID=' + Math.random()
    });
}



function ShowImportExcel(id) {
    layer.open({
        type: 2,
        title: '导入题目',
        shadeClose: true,
        shade: 0.8,
        area : ['500px' , '200px'],
        content: '/question-category/import-excel?id=' + id + '&SysID=' + Math.random()
    });
}
function UserImportExcel() {
    layer.open({
        type: 2,
        title: '导入项目用户数据',
        shadeClose: false,
        shade: 0.8,
        area : ['500px' , '200px'],
        content: '/question-project/import-excel?SysID=' + Math.random()
    });
}

function MeterImportExcel() {
    layer.open({
        type: 2,
        title: '导入项目抄表数据',
        shadeClose: false,
        shade: 0.8,
        area : ['500px' , '200px'],
        content: '/meter/import-excel?SysID=' + Math.random()
    });
}


function MeterUpdateImportExcel() {
    layer.open({
        type: 2,
        title: '更新项目抄表数据',
        shadeClose: false,
        shade: 0.8,
        area : ['500px' , '200px'],
        content: '/meter/update-import-excel?SysID=' + Math.random()
    });
}


function QuestionChoose(id) {
    layer.open({
        type: 2,
        title: '导入题目',
        shadeClose: true,
        shade: 0.8,
        area : ['800px' , '500px'],
        content: '/question-project/choose?id=' + id + '&SysID=' + Math.random()
    });
}
function AnswerChoose(id) {

    layer.open({
        type: 2,
        title: '答题详细',
        shadeClose: true,
        shade: 0.8,
        area : ['800px' , '600px'],
        content: '/question-project/answer-choose?id=' + id
    });
}
function devAnswerList(id) {

    layer.open({
        type: 2,
        title: '答题详细',
        shadeClose: true,
        shade: 0.8,
        area : ['800px' , '600px'],
        content: '/question-project/dev-answer-list?id=' + id
    });
}
function ActivityCopy(id) {
    layer.open({
        type: 2,
        title: '复制项目',
        shadeClose: true,
        shade: 0.8,
        area : ['400px' , '300px'],
        content: '/activity/copy?id=' + id + '&SysID=' + Math.random()
    });
}

function ActivityQrcode(id) {
    layer.open({
        type: 2,
        title: '项目二维码下载',
        shadeClose: true,
        shade: 0.8,
        area : ['400px' , '400px'],
        content: '/activity/show-qrcode?id=' + id + '&SysID=' + Math.random()
    });
}


function AClose()
{
    location.reload();
}