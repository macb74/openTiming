<?php	    
		    
		    $this->SetY(181);
		    $this->SetFont('Verdana','B',15);
		    //Calculate width of title and position
		    $titel = utf8_decode("3. Lauf zur MTB Bayernliga 2012");
		    $w=$this->GetStringWidth($titel);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$titel,0,0,'C',false);
		    $this->Ln(5);

		    $this->SetFont('Verdana','',10);
		    //Calculate width of title and position
		    $datum = utf8_decode("am 17. Juli 2012");
		    $w=$this->GetStringWidth($datum);
		    $this->SetX((210-$w)/2); // Mitte
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$datum,0,0,'C',false);
		    $this->Ln(22);
		    
		    
		    $this->SetFont('Verdana','B',22);
		    //Calculate width of title and position
		    $name = htmlspecialchars_decode(utf8_decode($row['nachname']), ENT_QUOTES).", ".htmlspecialchars_decode(utf8_decode($row['vorname']), ENT_QUOTES);
		    $w=$this->GetStringWidth($name);
		    $this->SetX((210-$w)/2); // Mitte
		    //$this->SetX(200 - $w); // Rechts
		    $this->SetTextColor(0);
		    $this->Cell($w,20,$name,0,0,'C',false);
		    $this->Ln(10);
				    
		    $this->SetFont('Verdana','B',12);
		    $verein = htmlspecialchars_decode(utf8_decode($row['verein']), ENT_QUOTES);
		    $w=$this->GetStringWidth($verein);
		    $this->SetX((210-$w)/2); // Mitte
		    //$this->SetX(200-$w);
		    $this->SetTextColor(0);
		    $this->Cell($w,15,$verein,0,0,'C',false);
		    $this->Ln(22);

		    
		    $this->SetFont('Verdana','B',22);
	    	$k = $platz.". Platz";
		    $w=$this->GetStringWidth($k);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$k,0,0,'C',false);
		    $this->Ln(6);

		    
		    $this->SetFont('Verdana','B',12);
			if($action == 'gesamt') {
	        	$platz = $row['platz'];
		    	$k = "in der Gesamtwertung";
			} else {
	        	$platz = $row['akplatz'];
	        	$k = "in der Klasse ".htmlspecialchars_decode(utf8_decode($row['klasse']), ENT_QUOTES);
			}
		    $w=$this->GetStringWidth($k);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$k,0,0,'C',false);
		    $this->Ln(5);

		   /* 
		    $this->SetFont('Verdana','B',12);
		    $zeit = "Zeit: ".$row['zeit'];
		    $w=$this->GetStringWidth($zeit);
		    $this->SetX((210-$w)/2);
		    $this->SetTextColor(0);
		    $this->Cell($w,10,$zeit,0,0,'C',false);
		    $this->Ln(0);
		   */
?>