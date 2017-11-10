<?php
/*
 * Created on 10.11.2017
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


function statistic() {
	if(isset($_GET['type'])) {
		$type = $_GET['type'];
		include "statistic/".$type.".php";
		$type();
	}
}


function getStatisticMenue() {
    
    $dir    = 'statistic';
    $files = scandir($dir);
?>
    <li class="dropdown">
	    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bar-chart"></i> Statistic <span class="caret"></span></a>
    	<ul class="dropdown-menu">

<?php   
    foreach ($files as $file) {
        if (preg_match ( '/.php$/',  $file )) {
            $fileWithoutSuffix = str_replace( ".php", "", $file);
            $fileDisp = str_replace( "_", " ", $fileWithoutSuffix);
            echo "<li><a href=\"index.php?func=statistic&type=$fileWithoutSuffix\">$fileDisp</a></li>";
        }
    }
?>
    	</ul>
    </li>

<?php     
    
}
?>