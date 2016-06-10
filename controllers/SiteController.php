<?php

class SiteController 
{

    public function actionIndex() 
    {
        $banners = array();
        for ($i = 0; $i < 3; $i++) {
            $banners[$i] = Banner::showBanner();
        }
        $currentPage = "/site/index.php";
        require_once ROOT . '/views/index.php';
    }

}
