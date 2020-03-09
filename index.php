
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


            default:
            header('HTTP/1.0 404 not found');
            require  __DIR__ .'/pages/error404.php';
          break;
       }

      

      
