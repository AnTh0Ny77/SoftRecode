<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Keyword;
use App\Tables\User;
use DateTime;
use App\Tables\Tickets;
use App\Apiservice\ApiTest;
use App\Tables\Article;
use App\Database;


class AddOfficeController extends BasicController {

    public static function displayList(){

        self::init();
        self::security();
        $Api = new ApiTest();
        $list = [];

        $token =  $Api->handleSessionToken2();

        
        if (!empty($_POST['add__titre'])) {
            $emplacement = "O:\myRecode\Promo"; 
            $nomFichier = strtoupper(self::nom_fichier_propre($_POST['add__titre'])); 
            $nomFichier = $nomFichier . '.PNG';
            $fichierTemporaire = 'add__img'; 
            $temp = self::sauvegarderFichierPNG($emplacement, $nomFichier, $fichierTemporaire);
        }

        if (!empty($_POST['add__titreEdit'])) {
            $emplacement = "O:\myRecode\Promo"; 
            $nomFichier = strtoupper(self::nom_fichier_propre($_POST['add__titreEdit'])); 
            $nomFichier = $nomFichier . '.PNG';
            $fichierTemporaire = 'add__imgEdit'; 
            $temp = self::sauvegarderFichierPNG($emplacement, $nomFichier, $fichierTemporaire);
        }

        $list_client = $Api->PostListClient($token,false)['data'];
        $list = $Api->getAdd($token , ['all' => 'vgvhnoza7875z85acc114cz5'])['data'];
        $definitive_arrray  = [];
        foreach ($list as $key => $value) {
            $temp = [
                'relation' => $value['relation'] , 
                'ad__img' => strtoupper(self::nom_fichier_propre($value['ad__titre'])). '.PNG',
                'ad__lien' => $value['ad__lien'] , 
                'ad__id' => $value['ad__id'] ,
                'ad__txt' => $value['ad__txt'] , 
                'ad__titre' => $value['ad__titre'] , 
                'img' => "data:image/png;base64," . base64_encode(file_get_contents('O:/myRecode/Promo/' . self::nom_fichier_propre($value['ad__titre']). '.PNG'))
            ];
            array_push($definitive_arrray,$temp);
        }

        return self::$twig->render(
            'display_add_list.html.twig',
            [
                'user' => $_SESSION['user'], 
                'list' => $definitive_arrray , 
                'list_client' => $list_client 
            ]
        );
    }


    public static function nom_fichier_propre($nom_fichier){
        $nom_fichier = trim($nom_fichier);
        $nom_fichier = str_replace(" ",          '_', $nom_fichier);
        $nom_fichier = str_replace("-",          '_', $nom_fichier);
        $nom_fichier = str_replace("'",          '_', $nom_fichier);
        $nom_fichier = str_replace("iso-8859-1", '',  $nom_fichier);
        $nom_fichier = str_replace('=E9',        'e', $nom_fichier);
        $nom_fichier = str_replace('=Q',         '',  $nom_fichier);
        $nom_fichier = str_replace('=',          '',  $nom_fichier);
        $nom_fichier = str_replace('?',          '',  $nom_fichier);
        $search =array('À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ');
        $replace=array('A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y');
        $nom_fichier = str_replace($search, $replace, $nom_fichier); // supprime les accents
        $nom_fichier = preg_replace('/([^_.a-zA-Z0-9]+)/', '', $nom_fichier);
        return strtoupper($nom_fichier);
    }

    public static function  sauvegarderFichierPNG($emplacement, $nomFichier, $fichierTemporaire){
        // Déplacer le fichier temporaire vers l'emplacement souhaité
        if (move_uploaded_file($_FILES[$fichierTemporaire]['tmp_name'], $emplacement . '/' . $nomFichier )) {
            return true; // Le fichier a été sauvegardé avec succès
        } else {
            return false; // Échec de sauvegarde du fichier
        }
    }

}