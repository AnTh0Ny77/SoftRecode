<?php
require "./vendor/autoload.php";
use App\Methods\Pdfunctions;
session_start();
//instanciation des entités nécéssaires au programme:
$Database = new App\Database('devis');
$Database->DbConnect();
$Cmd = new App\Tables\Cmd($Database);
$Client = new \App\Tables\Client($Database);
$Contact = new \App\Tables\Contact($Database);
$Keyword = new \App\Tables\Keyword($Database);
$User = new \App\Tables\User($Database);


// si pas connecté on ne vole rien ici :
if (empty($_SESSION['user'])) 
{
    header('location: login');
}
//sinon exécution du programme:
else 
{
    if (!empty($_POST['AjaxTransport']))
    {



     //recupere le devis:
     $temp =   $Cmd->GetById($_POST['AjaxTransport']);
     //recupere le createur du devis 
     $user = $User->getByID($temp->devis__user__id);
     //recupere le client: 
     $clientView = $Client->getOne($temp->client__id);
     //si societe de livraison, recupere la societe de livraison:
     $societeLivraison = false ;
     if ($temp->devis__id_client_livraison) 
     {
         $societeLivraison = $Client->getOne($temp->devis__id_client_livraison);
     }
     //recupere les lignes du devis:
     $arrayOfDevisLigne = $Cmd->devisLigne($_POST['AjaxTransport']);
     //cree une variable pour la date de commande du devis
     $date_time = new DateTime( $temp->cmd__date_cmd);
     //formate la date pour l'utilisateur:
     $formated_date = $date_time->format('d/m/Y');
     
  
  //a l'interieur de ob_start => contenu html du rendu:    
  ob_start();
  ?>


 <style type="text/css">
 .global-wrapper{min-height: 900px};
     .bodyFrame {font-family:Century Gothic,arial,sans-serif; }
     .div{   font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate;}
     .table{ font-size:13; font-style: normal; font-variant: normal;  border-collapse:separate;}
     strong{ color:#000;}
     .head-wrapper{
         display: flex;
         justify-content: space-around;
         padding-left: 5%;
     }
 </style>
 

 <div class="global-wrapper">
     <h1>hello</h1>
 </div>

<?php  


    //try catch pour l'envoi de la reponse:
    try 
    {
        //recupere tout le contenu HTMl depuis ob_start:
        $content = ob_get_contents();
        //vide le contenu ob:       
        ob_clean(); 
        //renvoi la data du devis en response JSOn !!! attention renvoi d'un array [0] pour html [1] pour ckeditor
        echo  json_encode($content);
    } 
    // si une exeption est attrapée
    catch (Exception $e) 
    {
        //renvoi l'erreur au format JSOn:
        echo  json_encode( $e->getMessage());
    }       

}
}