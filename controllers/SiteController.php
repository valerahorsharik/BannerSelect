<?php

class SiteController 
{

    public function actionIndex() 
    {
        
        $banner = array();
        $banner = Banner::takeBanners();
        $currentPage = "/site/index.php";
        require_once ROOT . '/views/index.php';
    }

}
