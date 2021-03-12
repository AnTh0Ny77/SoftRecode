<?php
require "./vendor/autoload.php";
use App\Methods\Pdfunctions;

session_start();
$Database = new App\Database('devis');
$Database->DbConnect();
$General = new App\Tables\General($Database);
$Cmd = new App\Tables\Cmd($Database);


if (empty($_SESSION['user']->id_utilisateur)) 
{
        header('location: login');
} 
else 
{
        // requete update:
        if (!empty($_POST['search']) && !empty($_POST['operator'])) 
        {
                $ligne = $Cmd->devisLigneUnit($_POST['search']);

                if ($_POST['operator'] == 'plus')
                {
                        $quantite = intVal($ligne->devl_quantite) + 1 ;
                }
                else 
                {
                        $quantite = intVal($ligne->devl_quantite) - 1 ;
                }
                if ($quantite < 0) 
                {
                        $quantite = 0 ;
                }
               
                $response = $General->updateAll('cmd_ligne' , $quantite ,'cmdl__qte_cmd', 'cmdl__id',$ligne->devl__id);
                $response = $Cmd->devisLigneUnit($_POST['search']);
                $devis = $Cmd->GetById($response->cmdl__cmd__id);
                $tableau_ligne = $Cmd->devisLigne_actif($response->cmdl__cmd__id);
                $totaux  = Pdfunctions::totalFacturePRO($devis, $tableau_ligne);
                $totaux[0] = number_format($totaux[0] , 2,',', ' ');
                $totaux[1] = number_format($totaux[1] , 2,',', ' ');
                $totaux[2] = number_format($totaux[2] , 2,',', ' ');
                $totaux[3] = number_format($totaux[3] , 2,',', ' ');
                $response->totaux = $totaux;
                echo json_encode($response);
        } else 
        {
                echo '{"error" : "requete incorrecte"}';
        }
}
