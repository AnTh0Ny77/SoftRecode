<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
use App\Tables\Stock;

class ExtranetController extends BasicController
{

    public static function login(): string
    {
        self::init();
        self::security();

        return self::$twig->render(
            'login_extranet.twig',
            [
               
            ]
        );
    }

}
