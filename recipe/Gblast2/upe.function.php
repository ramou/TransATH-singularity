<?php

$gevalue = @$argv[1];
$path = @$argv[2].'result/hits.out';

function getevalue(){
	
	return $evalue = @$argv[1];
	
}

function getpath(){
		
	$email = @$argv[2];	
	$path = $email.'result/hits.out';
	return $path;
	
}
function upe($acc) //Uniprot Extract
{
	$acc = trim($acc);
	$url = 'http://www.uniprot.org/uniprot/'.$acc.'.xml';
	if (!@file_get_contents($url)) 
	{ 
		return FALSE;
	}
		$fasta = file('http://www.uniprot.org/uniprot/'.$acc.'.fasta');
		
		$raw_seq = "";
		foreach ($fasta as $seq){
			if (preg_match("/(^\>.+)/", $seq, $hdr)){ $header = $hdr[1]; }
			$words = preg_split("/\n/", $seq);
			foreach( $words as $word){
				if (preg_match("/(^\w+)/", $word, $rseq)){ 
					$raw_seq .= $rseq[1];
				} 
			}
		}
		$xml = simplexml_load_file($url);

		foreach ($xml->entry as $entry) {
			$accession = $entry->accession;
		}
		foreach ($xml->entry as $entry) {
			$upname = $entry->name;
		}
		
		foreach ($xml->entry->protein as $protein) {
			if ($protein->recommendedName->fullName) {
				$protname = $protein->recommendedName->fullName;
			} else if ($protein->alternativeName->fullName) {
				$protname = $protein->alternativeName->fullName;
			} else if ($protein->submittedName->fullName) {
				$protname = $protein->submittedName->fullName; 
			}
		}

		foreach ($xml->entry->comment as $comment) {
			switch((string) $comment['type']) {
				case 'subcellular location':
				$location = $comment->subcellularLocation->location;
				case 'subcellular location':
				$topology = $comment->subcellularLocation->topology;
				$orientation = $comment->subcellularLocation->orientation;
			}
		}
		
		foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
			
				case 'Genevestigator':
				$Genevestigator = $dbRef['id'];
				break;
				
				case 'eggNOG':
   				$eggNOG = $dbRef['id'];
				break;
				
				case 'DIP':
				$DIP = $dbRef['id'];
				break;
				
				case 'HOGENOM':   
				$HOGENOM = $dbRef['id'];
				break;
				
				case 'EchoBASE':
				$EchoBASE = $dbRef['id'];
				break;
				
				case 'EcoGene':
 				$EcoGene = $dbRef['id'];
				break;	
			}
		}
		
		/**** EMBL -> GB Property Type -> GenBank ****/
		$EMBL_Type_GBs = array();
        foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'EMBL':
                $EMBL = $dbRef['id'];
				break;
			}
			if($dbRef['type'] == "EMBL") {
				$E2GType = $dbRef->property['type'];
				$GenBank = $dbRef->property['value'];
				array_push($EMBL_Type_GBs, $EMBL."\t".$E2GType."\t".$GenBank);
			}	
        }
		
		/**** RefSeq ****/
		$RefSeqs = array();
        foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'RefSeq':
                $RefSeq = $dbRef['id'];
                array_push($RefSeqs, $RefSeq);
			}
        }

		/**** PDB ****/
		$PDBs = array();
        foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'PDB':
                $PDB = $dbRef['id'];
				$type = $dbRef->property["type"];
				foreach ($dbRef->property as $prop) {
					switch((string) $prop['type']) {
						case 'method':
						$method = $prop["value"];
						break;
						case 'resolution':
						$resolution = $prop["value"];
						break;
					}
				}	
				array_push($PDBs, $PDB."\t".$method."\t".$resolution);
			}
        }

		/**** KEGG ****/
		$KEGGs = array();
        foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'KEGG':
                $KEGG = $dbRef['id'];
                array_push($KEGGs, $KEGG);
			}
        }
			
		$GOs = array();
		foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'GO':
				$GOID = $dbRef['id'];
				$GOtype = $dbRef->property["value"];
				array_push($GOs, $GOID."\t".$GOtype);
			}
		}
		
		$MIMs = array();
		foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'MIM':
				$MIM = $dbRef['id'];
				$MIMtype = $dbRef->property["value"];
				array_push($MIMs, $MIM."\t".$MIMtype);
			}
		}
		
		/**** Pfam ****/
		$Pfams = array();
		foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'Pfam':
                $Pfam = $dbRef['id'];
                array_push($Pfams, $Pfam);
			}
		}

		/**** Biocyc ****/
		$Biocycs = array();
        foreach ($xml->entry->dbReference as $dbRef) {
			switch((string) $dbRef['type']) {
				case 'Biocyc':
				$Biocyc = $dbRef['id'];
				array_push($Biocycs, $Biocyc);              
			}
        }

		/**** GID ****/
		$GeneIDs = array();
        foreach ($xml->entry->dbReference as $dbRef) {
  			switch((string) $dbRef['type']) {
				case 'GeneID':
				$GeneID = $dbRef['id'];
				array_push($GeneIDs, $GeneID);
			}
        }
		
			/**** Aliases ****/
	        foreach ($xml->entry->gene->name as $gene) {
				@$Aliases.="$gene ";
	        }
			$Aliases=trim($Aliases);
		
		foreach ($xml->entry->sequence as $sequence) {
			$length = $sequence['length'];
			$molwt = $sequence['mass'];
		}

		foreach ($xml->entry->organism->name as $name) {
			switch((string) $name['type']) {
				case 'scientific':
				$scientific = $name;
				$scientific = preg_replace("/\'/", "&#39;", $scientific); 
				break;
				case 'common':
				$common = $name;
				$common = preg_replace("/\'/", "&#39;", $common); 
				break;
			}
		}
	
		foreach ($xml->entry->organism->dbReference as $dbRef) {
			$taxid = $dbRef['id'];
		}
		
		$tmss = 0;
		foreach ($xml->entry->feature as $feature) {
			if ($feature['type'] == "transmembrane region") {
				$tmss++;
			}
		}
		
		/**** PUBMED References ****/
		$citation = array();
		$pub = array();
		$author = array();
		
		foreach ($xml->entry->reference as $dbRef) {
			if($dbRef->citation['type'] == "journal article") {
				$title = $dbRef->citation->title; 
				
				if($dbRef->citation->authorList->person['name']) {
					$a = $dbRef->citation->authorList->person['name'];
				} else if ($a = $dbRef->citation->authorList->consortium['name']) {
					$a = $dbRef->citation->authorList->consortium['name'];
				}
				
				foreach ($dbRef->citation->dbReference as $dbReference) {
					switch((string) $dbReference['type']) {
						case 'PubMed':
						$pmid = $dbReference['id']; 
						array_push($citation, $title."\t".$pmid."\t".$a);
						break;
					}
				}	
			}
		}
		
	
	//	return array($protname, $accession, $length, $molwt, $location, $scientific, $common, $header, $raw_seq); 
	return @array($upname, $protname, $accession, $length, $molwt, $scientific, $common, $taxid, $location, $topology, $orientation, $header, $raw_seq, $RefSeqs, $EMBL_Type_GBs, $PDBs, $KEGG, $Genevestigator, $GOs, $MIMs, $KEGGs, $Pfams, $Biocycs, $GeneIDs, $eggNOG, $DIP, $HOGENOM, $EchoBASE, $EcoGene, $tmss, $citation, $author,$Aliases); 


}
?>