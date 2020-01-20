
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

            default:
            header('HTTP/1.0 404 not found');
            require  __DIR__ .'/pages/error404.php';
          break;
       }

      

      
