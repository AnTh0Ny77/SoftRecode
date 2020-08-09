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
    if (!empty($_POST['AjaxFT']))
    {

        //recupere le devis:
        $temp =   $Cmd->GetById($_POST['AjaxFT']);
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
        $arrayOfDevisLigne = $Cmd->devisLigne($_POST['AjaxFT']);
        //cree une variable pour la date de commande du devis
        $date_time = new DateTime( $temp->cmd__date_cmd);
        //formate la date pour l'utilisateur:
        $formated_date = $date_time->format('d/m/Y');
        //formatte heure minute:
        $h = $date_time->format('H');
        $m = $date_time->format('i');
     
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
        <div class="div bodyFrame mt-4" style="width: 100%;" >
         <div class="head-wrapper div">


             <div class="div" style="text-align: left;  width: 50%;" >
             <div class="div">
             <img  style=" width:55mm;" src="public\img\recodeDevis.png"/>
             </div>
             <div class="div" style=" margin-top: 15%" >
             <h6><small>Commandé le : </small><b><?php echo $formated_date ?> à : <?php echo $h . 'h ' . $m ?></b></h6>
             <?php 
           
           $date = date_create( $temp->cmd__date_envoi);
           $date = date_format($date ,'d/m/Y');
           $dateText = 
           "<div class='' '>
               <h6> <small>Expédié le :</small>  <b>".$date."</b></h6>    
           </div>";
      
       
           echo $dateText;
       ?>
            <h6> 
                <small>Vendeur:</small> <b><?php echo  $user->nom . ' '. $user->prenom ?> </b> 
                <?php  if (!empty($user->postefix)) 
                {
                echo ' (Tél: '. $user->postefix .')';
                }  ?> 
             </h6>
             <?php
            

             if (!empty($temp->cmd__code_cmd_client)) 
             {
                echo 'code cmd client: <b>'.  $temp->cmd__code_cmd_client . '</b>';
             }

             ?>
             
             </td>
            
             </div>
            </div>
           
            <div class="div" style="text-align: left;  width: 45% ;"><h5>Commande N°:<strong> <?php echo $temp->devis__id ?></strong><br></h5>
            <?php 
             echo "<div><button class='btn btn-sm btn-success' id='ClientClick' value='".$temp->devis__id."' ><i class='far fa-magic'></i></i></button></div> ";
             // si une societe de livraion est présente 
             if ($societeLivraison) {

                    if ($temp->devis__contact__id) {
                        // si un contact est présent dans l'adresse de facturation :
                        $contact = $Contact->getOne($temp->devis__contact__id);
                        echo "<small>facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom. "</small><strong><br>";
                        echo Pdfunctions::showSociete($clientView) ." </strong> ";
                    
                        if ($temp->devis__contact_livraison) {
                            //si un contact est présent dans l'adresse de livraison : 
                            $contact2 = $Contact->getOne($temp->devis__contact_livraison);
                            echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        }
                        else {
                            // si pas de contact de livraison : 
                            echo "<br> <small>livraison :</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        } 
                    }

                    else {
                        echo "<small>facturation :</small><strong><br>";
                        echo Pdfunctions::showSociete($clientView) ." </strong>" ;
                        if ($temp->devis__contact_livraison) {
                            $contact2 = $Contact->getOne($temp->devis__contact_livraison);
                            echo "<br> <small>livraison : ".$contact2->contact__civ . " " . $contact2->contact__nom. " " . $contact2->contact__prenom."</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        } else {
                            echo "<br> <small>livraison :</small><strong><br>";
                            echo Pdfunctions::showSociete($societeLivraison) . "</strong></td>"; 
                        }  
                    }  
             } 

            

             else{
                if ($temp->devis__contact__id) {
                $contact = $Contact->getOne($temp->devis__contact__id);
                echo "<small>livraison & facturation : ". $contact->contact__civ . " " . $contact->contact__nom. " " . $contact->contact__prenom."</small><strong><br>";
                echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }
                else{
                    echo "<small>livraison & facturation : </small><strong><br>";
                    echo Pdfunctions::showSociete($clientView)  ."</strong></td>";
                }

             } 
            
             ?>
        
        <div class="d-flex flex-column">
          
            
          
           
            
        </div>

       </div>
      
      </div>
    
       
    </div>

    <div class="div" style="display: flex; justify-content: center; margin-top: 5%;">
        <table CELLSPACING=0 style="width: 95%;  margin-top : 30px;" class="table">


        <?php
            
        foreach ($arrayOfDevisLigne as $item) {
                        if(intval($item->cmdl__garantie_option) > intval($item->devl__mois_garantie)) 
                        {
                        $temp = $item->cmdl__garantie_option ;
                        } 
                        else 
                        { 
                        $temp = intval($item->devl__mois_garantie);
                        }
                        
                                echo "<tr style='font-size: 95%;'>
                                <td style='border-style: none; '> <button class='clickFact btn btn-success mt-2' value='".$item->devl__id."'  '><i class='far fa-magic'></i></i></button></td>
                                <td style='border-bottom: 1px #ccc solid; text-align:left; '>". $item->prestaLib." <br> " .$item->kw__lib ." <br> " . $temp ." mois </td>
                                <td style='border-bottom: 1px #ccc solid; '><strong> ".$item->famille__lib. " " . $item->modele . " ".$item->marque. "</strong> "   . $item->devl__modele . " <br><small>désignation sur le devis:</small> ".$item->devl__designation." <br>" .$item->devl__note_interne ." </td>
                                <td style='border-bottom: 1px #ccc solid;  text-align: right;  '><strong> "  . $item->devl_quantite. " </strong> </td>
                                </tr>";
                    }
           
               ?>
        </table> 
     </div>
     <div class="d-flex justify-content-end mr-3">

     
       
     <form class="text-right d-inline" method="POST" action="ficheTravail">
     <input type="hidden" value="<?php echo $comand->devis__id ?>" name="hiddenCommentaire">
    
     
     </form>
     </div>
    
    
     </div>
     
    

 
    <?php



        //try catch pour l'envoi de la reponse:
        try 
        {
            //recupere tout le contenu HTMl depuis ob_start:
            $content = ob_get_contents();

            //enregistre ou reecris le fichier de vision au nom du user + FT.html       
            $myfile = fopen(__DIR__ .'/'.$_SESSION['user']->log_nec.'FT.html', "w") ;
            $txt = $content;
            fwrite($myfile, $txt);
            fclose($myfile);
            //vide le contenu ob:       
            ob_clean();
            
            //renvoi la data du devis en response JSOn !!! attention renvoi d'un array [0] pour html [1] pour ckeditor
            echo  json_encode($content);
        } 
        // si une exeption est attrapée
        catch (Exception $e) 
        {
            //renvoi l'erreur au format JSOn:
            echo  json_encode( $e->getMessage('fuck'));
        }       
    
    //fin IF check le POST  
    }
 //fin IF check la connexion
 }