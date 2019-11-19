<?php 
define('URLFORM', 'https://stuweb.cms.gre.ac.uk/~ha07/web/PHP/mailformi.php');
define('URLLIST', 'https://stuweb.cms.gre.ac.uk/~ha07/web/PHP/maillistdbi.php');
$referer = $_SERVER['HTTP_REFERER'];

// if rererrer is not the form redirect the browser to the form
if ( $referer != URLFORM && $referer != URLLIST ) {
   header('Location: ' . URLFORM);
    exit();
}

if(!isset($_SERVER['HTTP_REFERER'])){
    responseError("Invalid origin request", 403, "Forbidden");
} else {
    $originReq = $_SERVER['HTTP_REFERER'];
    $allowed = false;
    $possibleOrigins = ["https://stuweb.cms.gre.ac.uk/~cp3526m/coursework/studentRecord.php",
                        "https://stuweb.cms.gre.ac.uk/~cp3526m/coursework/groupRecord.php", "https://stuweb.cms.gre.ac.uk/~cp3526m/coursework/welcomeTutor.php"];
     foreach($possibleOrigins as $el){
         if(strpos($originReq, $el) !== false){
             $allowed = true;
         }
     }   
    if(!$allowed){
         responseError("Invalid origin request", 403, "Forbidden");
    }
}
?>