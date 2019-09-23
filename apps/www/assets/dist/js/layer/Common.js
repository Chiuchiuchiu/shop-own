
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
        title: '测试的',
        shadeClose: true,
        shade: 0.8,
        area : ['500px' , '500px'],
        content: '/question-category/import-excel?id=' + id + '&SysID=' + Math.random()
    });
}