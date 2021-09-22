<?php
namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
use App\Tables\Stock;

Class ShowSpecController extends BasicController
{

	public static function show_spec($object) : string
	{
		self::init();
		self::security();
		$Stock = new Stock(self::$Db);
        $html = '';
        if ($object->apn__pn){

            $spec = $Stock->get_specs_pn_show($object->aap__pn);
        }
        else{

            $spec = $Stock->get_specs_modele_show($object->afmm__id);
        }

        if (!empty($spec)) 
        {
            $html .= ' <div style="color: blue">';

            foreach ($spec as $data)
            {
                if ($data->text_cle) 
                    $html .= ' ' .  $data->text_cle .' :';

                foreach ($data->$data as $key => $value) 
                {
                    $html .= ' ' . $value->valeur_txt ;

                    if ($key === array_key_last($data->$data)) {
                        $html .= '●';
                    }
                    else {
                        $html .= '-';
                    }
                }
            }

            $html .= '</div>';
        }
       
		return $html;
	}
}