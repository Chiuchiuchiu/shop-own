<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/16 10:46
 * Description:
 */

namespace components\inTemplate\widgets;


use components\inTemplate\assets\SummerNoteAsset;
use yii\web\View;
use yii\bootstrap\Widget;

class SummerNote extends Widget
{
    public function init()
    {
        parent::init();
        $view = $this->getView();
        SummerNoteAsset::register($view);
        $jsCode = <<<JS
        var __toolbar=[
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ];
        var __toolbarImg=[
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link','picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ];
        $("textarea[summerNote]").each(function(){
            var id = $(this).attr('id');
            var editId = id+'_summerNote';
            $(this).after($('<div id="'+editId+'"></div>'))
            $(this).hide();
            $('#'+editId).summernote({
            toolbar: $(this).attr('data-img')?__toolbarImg:__toolbar,
            lang:'zh-CN',
            height:$(this).attr('data-height')?$(this).attr('data-height'):120,
            fontNames: ['Arial', 'Arial Black',"microsoft yahei"],
            callbacks: {
                onChange: function(contents, editable) {
                    $('#'+id).val(contents);
                },
                onImageUpload: function(files) {
                    console.log(files);
                }
              }
            });
            $('#'+editId).summernote('code',$(this).val());

        });

JS;
        $view->registerJs(sprintf('$(function(){%s})', $jsCode), View::POS_END);
    }
}