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
use App\Tables\Abonnement;
use App\Database;


class MyRecodeSocieteController extends BasicController {
    public static function displayList(){
        self::init();
        self::security();
        $Api = new ApiTest();
        if (empty($_SESSION['user']->refresh_token)) {
            $token = $Api->login($_SESSION['user']->email , 'test');
            if ($token['code'] != 200) {
                echo 'Connexion LOGIN à L API IMPOSSIBLE';
                die();
            }
            $_SESSION['user']->refresh_token = $token['data']['refresh_token'] ; 
            $token =  $token['data']['token'];
        }else{
            $refresh = $Api->refresh($_SESSION['user']->refresh_token);
            if ( $refresh['code'] != 200) {
                echo 'Rafraichissemnt de jeton API IMPOSSIBLE';
                die();
            }
            $token =  $refresh['token']['token'];
        }

        $list = $Api->PostListClient($token,false)['data'];
       
        return self::$twig->render(
            'display_societe_myrecode_list.html.twig',[
                'user' => $_SESSION['user'] , 
                'list' => $list
            ]
        );
    }


    public static function postLogo(){
        self::init();
        self::security();
        $Database = new Database('devis');
        $Database->DbConnect();

        $path = "O:\myRecode";

        if (empty($_POST['cli__id'])) {
            header('Location: myRecode');
            exit();
        }
        if (!isset($_FILES['file']) && $file['logoInput']['error'] === UPLOAD_ERR_OK && file_exists($file['logoInput']['tmp_name'])) {
            header('Location: myRecode');
            exit();
        }

        self::InsereLogo($_POST['cli__id'], '' ,  $file['logoInput']['tmp_name']);
        header('Location: SocieteMyRecode?cli__id'.$_POST['cli__id']);
        exit();
        
    }

    public static function fichierPNGExiste($dossierPath) {
        // Vérifier si le dossier existe
        if (!is_dir($dossierPath)) {
            // Tenter de créer le dossier
            if (!mkdir($dossierPath, 0777, true)) {
                return null; // Échec de création du dossier
            }
        }
        // Obtenir le nom du dossier (sans le chemin)
        $dossierNom = basename($dossierPath);
    
        // Vérifier si le fichier PNG existe
        $fichierPNGPath = $dossierPath . DIRECTORY_SEPARATOR . $dossierNom . '.png';
        return file_exists($fichierPNGPath);
    }
    

    public static function InsereLogo($id, $path, $file) {
        $dossier = $path . $id;
        if (is_dir($dossier)){
            $dossierTech = $dossier . '/tech';
            $dossierAdmin = $dossier . '/administratif';
            if (!is_dir($dossierTech)){
                mkdir($dossierTech, 0777, true);
            }
            if (!is_dir($dossierAdmin)) {
                mkdir($dossierAdmin, 0777, true);
            }
            $nomFichier = $dossier . '/' . $id . '.png';
            file_put_contents($nomFichier, $file);
        }else{
            mkdir($dossier, 0777, true);
            $dossierTech = $dossier . '/tech';
            $dossierAdmin = $dossier . '/administratif';
            mkdir($dossierTech, 0777, true);
            mkdir($dossierAdmin, 0777, true);
            $nomFichier = $dossier . '/' . $id . '.png';
            file_put_contents($nomFichier, $file);
        }
    }

    public static function InsereDoc( $path, $option , $file , $extension) {
        $dossier = $path ;
        if (is_dir($dossier)){
            $dossierTech = $dossier . '/tech';
            $dossierAdmin = $dossier . '/administratif';
            if (!is_dir($dossierTech)){
                mkdir($dossierTech, 0777, true);
            }
            if (!is_dir($dossierAdmin)) {
                mkdir($dossierAdmin, 0777, true);
            }
            $emplacement = $dossier .  '/' . $option .  self::nom_fichier_propre($extension);
            move_uploaded_file($file , $emplacement );
        }else{
            mkdir($dossier, 0777, true);
            $dossierTech = $dossier . '/tech';
            $dossierAdmin = $dossier . '/administratif';
            mkdir($dossierTech, 0777, true);
            mkdir($dossierAdmin, 0777, true);
            $emplacement = $dossier . '/' . $option .  self::nom_fichier_propre($extension);
            move_uploaded_file($file , $emplacement );
        }
    }

    public static function listerDocumentsDansRepertoire($cheminRepertoire) {
        $documents = array();
        if (is_dir($cheminRepertoire)) {
            $fichiers = scandir($cheminRepertoire);
    
            foreach ($fichiers as $fichier) {
                // Ignorer les répertoires spéciaux "." et ".."
                if ($fichier != "." && $fichier != "..") {
                    // Vérifier si le chemin complet est un fichier
                    $cheminFichier = $cheminRepertoire . DIRECTORY_SEPARATOR . $fichier;
                    if (is_file($cheminFichier)) {
                        $documents[] = $fichier;
                    }
                }
            }
        }
        return $documents;
    }

    public static function supprimerFichier($cheminFichier) {
        if (file_exists($cheminFichier)) {
            if (unlink($cheminFichier)) {
                return true; // Le fichier a été supprimé avec succès.
            } else {
                return false; // Erreur lors de la suppression du fichier.
            }
        } else {
            return false; // Le fichier n'existe pas.
        }
    }
     

    public static function  sauvegarderFichierPNG($emplacement, $nomFichier, $fichierTemporaire){
            // Déplacer le fichier temporaire vers l'emplacement souhaité
            if (move_uploaded_file($_FILES[$fichierTemporaire]['tmp_name'], $emplacement . '/' . $nomFichier . '.png')) {
                return true; // Le fichier a été sauvegardé avec succès
            } else {
                return false; // Échec de sauvegarde du fichier
            }
    }

    public static function ajouterZeros($chaine) {
        $longueurChaine = strlen($chaine);
        
        if ($longueurChaine < 6) {
            $nombreZeros = 6 - $longueurChaine;
            $zeros = str_repeat('0', $nombreZeros);
            $chaineAvecZeros = $zeros . $chaine;
            return $chaineAvecZeros;
        } else {
            return $chaine;
        }
    }

     public static function detecterExtensionFichier($fichier) {
        $nomFichier = $fichier['name'];
        $extension = pathinfo($nomFichier, PATHINFO_EXTENSION);
    
        return $extension;
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
    
    

    public static function display(){
        self::init();
        self::security();
        $Database = new Database('devis');
        $Database->DbConnect();
        $Api = new ApiTest();

        $Article = new Article($Database);
        $Abonnement = new Abonnement($Database);

        if ($_SESSION['user']->user__cmd_acces < 10 ){
            header('location: noAccess');
            die();
        }

        if(empty($_SESSION['user']->refresh_token)){
            $token = $Api->login($_SESSION['user']->email, 'test');
            if ($token['code'] != 200) {
                echo 'Connexion LOGIN à L API IMPOSSIBLE';
                die();
            }
            $_SESSION['user']->refresh_token = $token['data']['refresh_token'];
            $token =  $token['data']['token'];
        }else{
            $refresh = $Api->refresh($_SESSION['user']->refresh_token);
            if ($refresh['code'] != 200) {
                echo 'Rafraichissemnt de jeton API IMPOSSIBLE';
                die();
            }
            $token =  $refresh['token']['token'];
        }
       
        if (empty($_GET['cli__id'])) {header('location SocieteMyRecode');die();}

        $client = $Api->PostListClient($token, $_GET['cli__id'])['data'];
        $list_client = $Api->PostListClient($token,false)['data'];

        $abn = $Abonnement->getByClinet($_GET['cli__id']);
        

        if (empty($client)){header('location SocieteMyRecode');die();}

        $pn_list = $Article->getModelsMyRecode();
        $body  = [
            'secret' => "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528" , 
            'shop_avendre' => true
        ];
        $list_avendre = $Api->getShopVendre($token,$body)['data'];

        $image_name = self::ajouterZeros($_GET['cli__id']);
        $logo = self::fichierPNGExiste("O:\myRecode/".$image_name);

        if (!empty($logo)){ $logo = "data:image/png;base64," . base64_encode(file_get_contents('O:/myRecode/' . $image_name . '/' . $image_name . '.png'));}

        if (!empty($_POST['clicli'])){
            $emplacement = "O:\myRecode/".$image_name; 
            $nomFichier = $image_name; 
            $fichierTemporaire = 'logoInput'; 
            $temp = self::sauvegarderFichierPNG($emplacement, $nomFichier, $fichierTemporaire);
            $logo = "data:image/png;base64," . base64_encode(file_get_contents('O:/myRecode/' . $image_name . '/' . $image_name . '.png'));
        }

        $emplacement = 'O:\myRecode/' . self::ajouterZeros($_GET['cli__id']) ;

        if (!empty($_POST['docNameAd'])) {
            self::supprimerFichier($emplacement . '/administratif' . '/' . $_POST['docNameAd']);
        }

        if (!empty($_POST['docNameTec'])) {
            self::supprimerFichier($emplacement . '/tech' . '/' . $_POST['docNameTec']);
        }

         //post///
         if (!empty($_POST['docRadio'])) {
            if ($_POST['docRadio'] === 'ad') {
                $tempPath = 'administratif/';
            }else {$tempPath = 'tech/';}
            $extension = self::detecterExtensionFichier($_FILES['DocInput']);
            $nomFichierOriginal = $_FILES['DocInput']['name'];
            self::InsereDoc($emplacement , $tempPath , $_FILES['DocInput']['tmp_name'] , $nomFichierOriginal );
        }

        $admin_list = self::listerDocumentsDansRepertoire($emplacement . '/administratif' );
        $tech_list = self::listerDocumentsDansRepertoire($emplacement. '/tech' );
      
        header("Access-Control-Allow-Origin: *");
        return self::$twig->render(
            'display_societe_myrecode.html.twig',
            [
                'user' => $_SESSION['user'],
                'abn' => $abn ,
                'client' => $client , 
                'pn_list' => $pn_list , 
                'list_client' => $list_client ,
                'avendre_list' => $list_avendre , 
                'logo' => $logo , 
                'admin_list' => $admin_list , 
                'tech_list' => $tech_list
            ]
        );
    }
    
}