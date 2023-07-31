<?php
namespace App\Controller;
require_once  '././vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
  }
  
  
use App\Database;

Class BasicController 
{

	protected static $twig;
    protected static $Db ;

	protected static function init()
	{
        self::$Db = new Database('devis');
        self::$Db->DbConnect();

		$loader = new \Twig\Loader\FilesystemLoader('./public/template/');
       	self::$twig = new \Twig\Environment($loader, [
           'debug' => true,
           'cache' => false,
       	]);
       	self::$twig->addExtension(new \Twig\Extension\DebugExtension());
	}

	protected static function security()
	{
		if (empty($_SESSION['user']->id_utilisateur))
		{
			header('location: login');
			die();
		}					
	}

	public static function check_post(array $array_post , string $redirection)
	{
		foreach($array_post as $post)
		{
			if (empty($_POST[$post])) 
			{
				header('location  '.$redirection.'');
			}
		}
	}
   
}

