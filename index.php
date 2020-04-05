
  <?php 
       
       $request = $_SERVER['REQUEST_URI'];
      
       switch($request){
            case  '/DevisRecode/'. '':
              require __DIR__ .'/pages/login.php';
            break;

            case '/DevisRecode/login':
              require __DIR__ .'/pages/login.php';
            break;

            case '/DevisRecode/home':
              require __DIR__ .'/pages/home.php';
            break;

            case '/DevisRecode/nouveauDevis';
             require __DIR__ .'/pages/nouveauDevis.php';
            break;

            case '/DevisRecode/mesDevis';
            require __DIR__ .'/pages/mesDevis.php';
           break;

           case '/DevisRecode/unlog';
            require __DIR__ .'/pages/utilities/unlog.php';
           break;

           case '/DevisRecode/pdf';
            require __DIR__ .'/pages/utilities/pdf.php';
           break;

           case '/DevisRecode/voirDevis';
            require __DIR__ .'/pages/utilities/viewPdf.php';
           break;

           case '/DevisRecode/AjaxSociete';
            require __DIR__ .'/pages/ajax/ajaxClient.php';
           break;

           case '/DevisRecode/AjaxDevis';
            require __DIR__ .'/pages/ajax/ajaxDevis.php';
           break;
           
           case '/DevisRecode/commande';
            require __DIR__ .'/pages/commandValid.php';
           break;

           case '/DevisRecode/saisie';
            require __DIR__ .'/pages/saisie.php';
           break;

           case '/DevisRecode/saisieLivraison';
            require __DIR__ .'/pages/saisieValid.php';
           break;

           case '/DevisRecode/createNew';
            require __DIR__ .'/pages/ajax/ajaxCreate.php';
           break;

           case '/DevisRecode/createClient';
           require __DIR__ .'/pages/ajax/ajaxCreateClient.php';
           break;

           case '/DevisRecode/tableContact';
           require __DIR__ .'/pages/ajax/ajaxTableContact.php';
           break;

           case '/DevisRecode/choixContact';
           require __DIR__ .'/pages/ajax/ajaxcontactChoix.php';
           break;

           case '/DevisRecode/createContact';
           require __DIR__ .'/pages/ajax/ajaxcreateContact.php';
           break;

           case '/DevisRecode/choixLivraison';
           require __DIR__ .'/pages/ajax/ajaxChoixLivraison.php';
           break;

           case '/DevisRecode/commandCours';
           require __DIR__ .'/pages/commandCours.php';
           break;

           case '/DevisRecode/pdfTravail';
            require __DIR__ .'/pages/utilities/pdfTravail.php';
           break;

           case '/DevisRecode/pdfBL';
            require __DIR__ .'/pages/utilities/pdfBL.php';
           break;

           case '/DevisRecode/AjaxCMDcours';
            require __DIR__ .'/pages/ajax/ajaxCMDcours.php';
           break;

           case '/DevisRecode/AjaxSaisie';
            require __DIR__ .'/pages/ajax/ajaxSaisie.php';
           break;

           case '/DevisRecode/utilisateurs';
            require __DIR__ .'/pages/utilisateurs.php';
           break;

           case '/DevisRecode/addUser';
            require __DIR__ .'/pages/addUser.php';
           break;

           case '/DevisRecode/createUser';
           require __DIR__ .'/pages/utilities/formUser.php';
          break;

          case '/DevisRecode/modifyForms';
          require __DIR__ .'/pages/utilities/formUserModify.php';
          break;

          case '/DevisRecode/noAccess';
          require __DIR__ .'/pages/noAccess.php';
          break;

          case '/DevisRecode/modifyUser';
          require __DIR__ .'/pages/modifyUser.php';
          break;

          
            default:
            header('HTTP/1.0 404 not found');
            require  __DIR__ .'/pages/error404.php';
          break;
       }

      

      
