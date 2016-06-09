<?php

class SiteController 
{

    public function actionIndex() 
    {
    
        $currentPage = "/site/index.php";
        require_once ROOT . '/views/index.php';
    }

}
