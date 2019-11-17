<?php 
define('URLFORM', 'https://stuweb.cms.gre.ac.uk/~ha07/web/PHP/mailformi.php');
define('URLLIST', 'https://stuweb.cms.gre.ac.uk/~ha07/web/PHP/maillistdbi.php');
$referer = $_SERVER['HTTP_REFERER'];

// if rererrer is not the form redirect the browser to the form
if ( $referer != URLFORM && $referer != URLLIST ) {
   header('Location: ' . URLFORM);
}
?>