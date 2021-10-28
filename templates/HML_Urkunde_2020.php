<?php
	if ($action != 'team') {
	
		    $this->SetY(175);
		    $this->SetFont('Verdana','B',16);
		    //Calculate width of title and position
		    $titel = utf8_decode("15. Holzkirchner Marktlauf");
		    $w=$this->GetStringWidth($titel);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$titel,0,0,'C',false);
		    $this->Ln(7);

		    $this->SetFont('Verdana','',12);
		    //Calculate width of title and position
		    $datum = utf8_decode("vom 01.10.20 bis 31.10.2020");
		    $w=$this->GetStringWidth($datum);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$datum,0,0,'C',false);
		    $this->Ln(6);

		    $this->SetFont('Verdana','',12);
		    //Calculate width of title and position
		    $datum = htmlspecialchars_decode(utf8_decode("über ".$raceData['LUntertitel']), ENT_QUOTES);
		    $w=$this->GetStringWidth($datum);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$datum,0,0,'C',false);
		    $this->Ln(15);
		    
		    $this->SetFont('Verdana','B',22);
		    //Calculate width of title and position
		    $name = htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES);
		    $w=$this->GetStringWidth($name);
		    $this->SetX((210-$w)/2); // Mitte
		    //$this->SetX(200 - $w); // Rechts
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$name,0,0,'C',false);
		    $this->Ln(10);
				    
		    $this->SetFont('Verdana','B',16);
		    $verein = htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES);
		    $w=$this->GetStringWidth($verein);
		    $this->SetX((210-$w)/2); // Mitte
		    //$this->SetX(200-$w);
		    $this->SetTextColor(0);
		    $this->Cell($w,15,$verein,0,0,'C',false);
		    $this->Ln(20);

		    $this->SetFont('Verdana','B',22);
	    	$k = $platz.". Platz";
		    $w=$this->GetStringWidth($k);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$k,0,0,'C',false);
		    $this->Ln(7);

		    
		    $this->SetFont('Verdana','B',16);
			if($action == 'gesamt') {
	        	$platz = $row['platz'];
		    	$k = "in der Gesamtwertung";
			} else {
	        	$platz = $row['akplatz'];
	        	$k = "in der Klasse ".utf8_decode($row['klasse']);
			}
		    $w=$this->GetStringWidth($k);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$k,0,0,'C',false);
		    $this->Ln(8);

		    $this->SetFont('Verdana','B',16);
		    $zeit = "Zeit: ".$row['zeit'];
		    $w=$this->GetStringWidth($zeit);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$zeit,0,0,'C',false);
		    $this->Ln(0);

	} else {
			
		    $this->SetY(177);
		    $this->SetFont('Verdana','B',16);
		    //Calculate width of title and position
		    $titel = utf8_decode("15. Holzkirchner Marktlauf");
		    $w=$this->GetStringWidth($titel);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$titel,0,0,'C',false);
		    $this->Ln(7);

		    $this->SetFont('Verdana','',12);
		    //Calculate width of title and position
		    $datum = utf8_decode("vom 01.10.20 bis 31.10.2020");
		    $w=$this->GetStringWidth($datum);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$datum,0,0,'C',false);
		    $this->Ln(6);
		    
		    $this->SetFont('Verdana','',12);
		    //Calculate width of title and position
		    $datum = htmlspecialchars_decode(utf8_decode($raceData['LUntertitel']." Teamwertung"), ENT_QUOTES);
		    $w=$this->GetStringWidth($datum);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$datum,0,0,'C',false);
		    $this->Ln(15);

		    $this->SetFont('Verdana','B',20);
		    $verein = htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES);
		    $w=$this->GetStringWidth($verein);
		    $this->SetX((210-$w)/2); // Mitte
		    //$this->SetX(200-$w);
		    $this->SetTextColor(0);
		    $this->Cell($w,15,$verein,0,0,'C',false);
		    $this->Ln(6);
		    
			$i = 1;
			$len = count($row['Name']);
		    while ($i <= $len) {
			    $this->SetFont('Verdana','',12);
			    //Calculate width of title and position
			    $name = htmlspecialchars_decode(utf8_decode($row['Name'][$i]), ENT_QUOTES);
			    $w=$this->GetStringWidth($name);
			    $this->SetX((210-$w)/2); // Mitte
			    //$this->SetX(200 - $w); // Rechts
			    $this->SetTextColor(0);
			    $this->Cell($w,20,$name,0,0,'C',false);
			    $this->Ln(5);
			    $i++;
		    }
			$this->Ln(10);
		    
		    $this->SetFont('Verdana','B',20);
	    	$k = $row['vplatz'].". Platz";
		    $w=$this->GetStringWidth($k);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$k,0,0,'C',false);
		    $this->Ln(8);
		    
		    $this->SetFont('Verdana','B',16);
		    $zeit = "Zeit: ".$row['vtime'];
		    $w=$this->GetStringWidth($zeit);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$zeit,0,0,'C',false);
		    $this->Ln(0);

	}
?>
