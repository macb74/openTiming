<?php

function getChatMessage($id) {
	$sql = "select * from chat where id > ".$id.";";
	$result = dbRequest($sql, 'SELECT');
	
	$html = "";
	
	if($result[1] > 0) {
		foreach ($result[0] as $row) {
			$html .= "<small>".$row['timestamp']."</small><br><b>".$row['message']."</b><br>";
			$id = $row['ID'];
		}
	}
	
	$out['id'] = $id;
	$out['message'] = $html;
	
	echo json_encode($out);
}

function addChatMessage() {
	$sql = "insert into chat (message) values ('".$_POST['new-chat-message']."');";
	$result = dbRequest($sql, 'INSERT');
	echo "O.K";
}