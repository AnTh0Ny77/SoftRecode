<?php

namespace App\Controller;
require_once  '././vendor/autoload.php';
use App\Controller\BasicController;
use App\Tables\Article;
use App\Tables\Keyword;
use App\Database;
use PDO;
use DateTime;
use App\Tables\Stock;

class PlanningController extends BasicController{

    public static function planning(){
        self::init();
        self::security();

        $list_time = self::getTimes();
      
        $list_time = json_encode($list_time);

        return self::$twig->render(
            'planning.twig',[
                'user' => $_SESSION['user'] , 
                'list_time' => $list_time
            ]
        );
    }


    public static function getTimes(){
        $Database = new Database('devis');
        $Database->DbConnect();
        $request = $Database->Pdo->query("SELECT DISTINCT  t.* , u.prenom , u.nom
        FROM time_out as t
        LEFT JOIN utilisateur as u ON u.id_utilisateur  = t.to__user
        LIMIT 20000");
        $data = $request->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public static function traiterDate($dateSQL){
        // Convertir la date SQL en objet DateTime
        $date = new DateTime($dateSQL);
        // Vérifier si l'heure est plus tardive que 9h du matin
        if ($date->format('H:i:s') < '09:00:00') {
            // Supprimer la précision heure minute seconde
            $date->setTime(0, 0, 0);
            // Retourner la nouvelle chaîne
            return $date->format('Y-m-d');
        } else {
            // Si l'heure n'est pas plus tardive que 9h du matin, retourner la date originale
            return $dateSQL;
        }
    }

}
