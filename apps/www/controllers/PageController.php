<?php
/**
 * Created by
 * Author: zhao
 * Time: 2017/2/9 01:19
 * Description:
 */

namespace apps\www\controllers;


class PageController extends Controller
{
    public function actionAbout(){
        return $this->render('about',[]);
    }
}