<?php
namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
use App\Tables\Stock;

Class RechercheController extends BasicController
{

	public static function recherche_famille() : string
	{
		self::init();
		self::security();

		$Article = new Article(self::$Db);
		$famille_list = $Article->getFAMILLE();

		return self::$twig->render(
				'recherches_famille.twig',
			[
				'user' => $_SESSION['user'] ,
				'famille_list' => $famille_list
			]
			);	
	}


	public static function recherche_spec() : string
	{
		self::init();
		self::security();
		self::check_post(['famille'],'recherche-articles-famille');

		$Stocks = new Stock(self::$Db);
		$Keyword = new Keyword(self::$Db);

		$forms_data = 	$Stocks->get_famille_forms($_POST['famille']);
        $object     = 	$Keyword->get_kw_by_typeAndValue('famil', $_POST['famille'] );
		$forms = null;
		
		if (!empty($_POST['rechercheJSON'])) {
			$forms = json_decode($_POST['rechercheJSON']);
			$forms = (array)$forms;

			foreach ($forms as $key => $value) {
					if (!is_array($value)) {
						unset($forms[$key]);
					}
			}
		}

		
		return self::$twig->render(
				'recherches_specs.twig',
			[
				'user' => $_SESSION['user'] , 
				'forms_data' => $forms_data ,
				'object' => $object ,
				'forms' => $forms
			]
		);	
	}

	public static function recherche_results() : string
	{
		self::init();
		self::security();
		self::check_post(['famille'],'recherche-articles-specs');

		$Stocks = new Stock(self::$Db);
		$Keyword = new Keyword(self::$Db);
		$Article = new Article(self::$Db);
		$famille = $Keyword->get_kw_by_typeAndValue('famil', $_POST['famille'] );
		$forms_data = $Stocks->get_famille_forms($_POST['famille']);
		$results_list = $Stocks->find_models_spec($forms_data, $_POST);
		$results_models = [];
		foreach ($results_list as $results) 
		{
			$article = $Article->get_fmm_by_id($results->aam__id_fmm);
			$specs = $Stocks->get_specs_models($results->aam__id_fmm);
			$article->specs = $specs;
			array_push($results_models , $article );
		}
		
	
		return self::$twig->render(
				'recherches_results.twig',
			[
				'user' => $_SESSION['user'] , 
				'famille' => $famille ,
				'stock' => $Stocks ,
				'results_list' => $results_models , 
			
			]
		);	
	}
}

