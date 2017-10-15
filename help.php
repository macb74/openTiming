<?php
/*
 * Created on 15.10.2017
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include('Classes/Parsedown.php');

function getHelpMenue() {
    
    $dir    = 'info';
    $files = scandir($dir);
?>
    <li class="dropdown">
	    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-info-circle "></i> Hilfe <span class="caret"></span></a>
    	<ul class="dropdown-menu">

<?php   
    foreach ($files as $file) {
        if (preg_match ( '/.md$/',  $file )) {
            $fileDisp = str_replace( ".md", "", $file);
            $fileDisp = str_replace( "_", " ", $fileDisp);
            echo "<li><a href=\"#\" onclick=\"showHelpMessage('$file'); return false;\">$fileDisp</a></li>";
        }
    }
?>
    	</ul>
    </li>

<?php     
    
}


function getHelpMessage($file) {
    $file = base64_decode($file);
    $content = file_get_contents('info/'.$file);
    $Parsedown = new Parsedown();
    echo $Parsedown->text($content);
}

?>