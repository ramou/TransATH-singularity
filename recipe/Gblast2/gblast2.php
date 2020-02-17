#!/usr/bin/php
<?php
##############################################################################
# GBLAST2 - By Vamsee Reddy <Symphony.Dev@gmail.com                          #
# This program will sort your hits.out generated from TMS GBLAST by TCID     #
# And add extra annotations, and provide the top hits according to thresholds#
# Will also provide TMS numbers. Make sure you have HMMTOP installed.        #
# THis scirpt is automatically executed after GBLAST - MODIFIED              #
##############################################################################
ini_set('memory_limit','2048M');
require('upe.function.php');
//$evalue = @$argv[1];
//$path = @$argv[2].'result/hits.out';
Class Genome
{

	//var $file='result/hits.out';
	var $file;
	var $data;
	var $tc_families;
	var $genomefile='g.fsa';
	var $genomes;
	var $handle;
	
	function __Construct($file,$genome,$out)
	{
		//$this->file=getpath();
		global $argv;
		$this->file = $argv[2].'result/hits.out';
		$this->genomefile=$genome;
		$this->load_data();
		$this->load_tcdb_families();
		$this->sort_tcids();
		$this->open_handle($out);
		$this->process_data();
	}
	
	public function load_data()
	{
		$data=trim(file_get_contents($this->file));
		$data=explode("\n",$data);
		unset($data[0]);
		foreach($data as $row)
		{
			$line=explode("\t",$row);
			$keys[$line[2]][]=$line;
		}
		$this->data=$keys;
		$genome=trim(file_get_contents($this->genomefile));
		$genome=explode(">",$genome);
		foreach($genome as $g)
		{
			$id=explode("\n",$g);
			$seq=$id[1];
			$idtmp=explode(" ", $id[0]); //split the ids and seq by space			
			$ids=$idtmp[0]; //to just take the first part of the id
			echo "\n Id: $ids Seq: $seq";
			$seq=explode("\n",$g); //this three lines is to count the TMS
			unset($seq[0]);	//		
			$seq=implode("\n",$seq); //			
			$genomes[$ids]=$seq;
		}
		$this->genomes=$genomes;
	}
	
	public function load_tcdb_families()
	{
		$url="file:///var/www/html/Gblast2/families.tsv";
		$c=file_get_contents($url);
		$c=trim($c);
		$c=explode("\n",$c);
		foreach($c as $line)
		{
			$line=explode("\t",$line);
			$res[$line[0]]=$line[1];
		}
		return $this->tc_families=$res;
	}
	
	public function multi_implode($glue, $pieces)
	{
	    $string='';

	    if(is_array($pieces))
	    {
	        reset($pieces);
	        while(list($key,$value)=each($pieces))
	        {
	            $string.=$glue.$this->multi_implode($glue, $value);
	        }
	    }
	    else
	    {
	        return $pieces;
	    }

	    return trim($string, $glue);
	}
	
	public function sort_tcids()
	{
		global $argv;	
		$keys=array_keys($this->data);
		foreach($keys as $tcid)
		{
			$key=explode(".",$tcid);
			$tc[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][]=$tcid;
		}
		ksort($tc);
		foreach($tc as $key=>$part)
		{
			ksort($tc[$key]);
			foreach($tc[$key] as $key2=>$part2)
			{
				ksort($tc[$key][$key2]);
				foreach($tc[$key][$key2] as $key3=>$part3)
				{
					ksort($tc[$key][$key2][$key3]);
					foreach($tc[$key][$key2][$key3] as $key4=>$part4)
					{
						ksort($tc[$key][$key2][$key3][$key4]);
					}
				}
			}
		}
		$tc=$this->multi_implode("\n",$tc);
		$tc=explode("\n",$tc);
		foreach($tc as $t)
		{
			$id=explode('.',$t);
			$family="{$id[0]}.{$id[1]}.{$id[2]}";
			//$pn="{$id[3]}.{$id[4]}"; //protein id
			$append=array($family);//,$pn);
			foreach($this->data[$t] as $key=>$entry)
			{
				$insert=array_merge($append,$entry);
				$data[$t][$key]=$insert;
                        
			}
		}
		$this->data=$data;
		foreach($this->data as $tcid=>$entries)
		{
			$e=array();
			$app=1;
			$matchold="";
			foreach($entries as $entry)
			{
				$match=preg_replace('/%/','',$entry[7]);
				if($match == $matchold)
				{
					$match = $match . $app; //connecting the string with numbers for counting (% coverage + counter)
					$app++;
					echo "\nmatch: $match\n Entry: ";
					print_r($entry);
				}
				else
				{
					$matchold = $match;
				}
				$e[$match]=$entry;				
			}
			ksort($e);
			$e=array_reverse($e);
			$ak=array_keys($e);
			print_r($e);
			// output the top hit and all hits > 70% 
			//$top=$ak[0]; //the highest hit
			//$datas[$e[$top][2]]=$e[$top];
			foreach ($ak as $akvalue)
			{
echo "\n >>Qcov: ";
print_r($e[$akvalue][8]);
echo "\n >>Percent_Diff: ";
print_r($e[$akvalue][6]);
echo "\n>>e[] $akvalue";
//echo "EVALUE =>".$argv[1];
			
				if($argv[1] == "30") {
					if ($akvalue>=30 && $e[$akvalue][8] >= 70 && $e[$akvalue][6] <= 10) //% identity, Qcov and %Diff
						{$datas[$e[$akvalue][10]] = $e[$akvalue];}
				}
				
				elseif($argv[1] == "40") {
					if ($akvalue>=40 && $e[$akvalue][8] >= 70 && $e[$akvalue][6] <= 10) //% identity, Qcov and %Diff
						{$datas[$e[$akvalue][10]] = $e[$akvalue];}
				}
				
				elseif($argv[1] == "50") {
					if ($akvalue>=50 && $e[$akvalue][8] >= 70 && $e[$akvalue][6] <= 10) //% identity, Qcov and %Diff
						{$datas[$e[$akvalue][10]] = $e[$akvalue];}
				}
				elseif($argv[1] == "60") {
					if ($akvalue>=60 && $e[$akvalue][8] >= 70 && $e[$akvalue][6] <= 10) //% identity, Qcov and %Diff
						{$datas[$e[$akvalue][10]] = $e[$akvalue];}
				}
				elseif($argv[1] == "70"){
                                        if ($akvalue>=70 && $e[$akvalue][8] >= 70 && $e[$akvalue][6] <= 10) //% identity, Qcove and %Diff
                                                {$datas[$e[$akvalue][10]] = $e[$akvalue];}
				}
			}
			
		}
		$this->data=@$datas;
				
		//print_r($this->data); exit;
			//	$this->data=$sorted;
	}
	
	public function query_tms($sequence)
	{
		ob_start();
		$uid = "tms_count.seq";
		$tmp='/tmp';
		$fpseqfile1 = fopen("$tmp/$uid", "w+");
		fwrite($fpseqfile1, ">MY_SEQ\n$sequence");
		fclose($fpseqfile1);
		$command = "/var/www/html/Gblast2/hmmtop -if=$tmp/$uid";
		$can = system($command);
		ob_end_clean();
		$chunks = preg_split("/\s+/", $can, 5);
		if(isset($chunks [4])) $chunks = preg_split("/\s+/", $chunks[4]);
		echo "chunk: $chunks[3] \t $can";
		unlink("$tmp/$uid");
		return $chunks[0];
	}
	
	public function process_data()
	{
		foreach($this->data as $row)
		{

				$fname=$this->tc_families[$row[0]];
				$block=$row;
				$upe=upe($row[3]);
				$htms=($upe[29])?$upe[29]:$this->query_tms($upe[12]);
				$block[8]=$htms;
				echo "\n>>8 $block[8]";
				$qseq=$this->genomes[$row[1]]; //id of the seq in hits.out is the key for the sequence
				//echo "\n Row: $row[2] seq: $qseq";
				$qtms=$this->query_tms($qseq);
				$block[4]=$qtms;
				//echo "\n>>7 $block[7]";
				$block[]=$fname;
				//$line=implode("\t",$block);
				if ($qtms > 0 && $block[5]>0) //check for qtms and htms > 0
					fwrite($this->handle,"$block[0]\t$block[14]\t$block[3]\t$block[2]\t$block[5]\t$block[12]\t$block[13]\t$block[1]\t$block[4]\n");
print_r($block);
				echo "\n>> Wrote ID: {$row[2]} Fname: $fname";

		}
		echo "\n\n>> PROCESS COMPLETE";
	}
	
	public function open_handle($file)
	{

		$handle=fopen("$file","w+");
		$this->handle=$handle;
		//$head="Family_ID#\tFamily_Name\tTCID\tHit\tHit_TMS\tSubstrate_Group\tSpec_Substrate\tQuery\tQry_TMS\n";
		$head="Family_TC#\tFamily_Name\tHit_TCID\tAcc.in_TCDB\tHit_TMS#\tSubstrate_Group\tSpec_Substrate\tSeq_ID#\tQry_TMS#\n";
		#Original clean.tsv output
		#$head="Family_ID\tProtein_ID\tQuery\tHit\tTCID\tQry_Description\tHit_Description\tQry_TMS\tHit_TMS\tQry_Length\tSub_Length\tQry_Region\tHit_Region\t%_Identity\thsp_hit-length\thsp_total-length\tQcov\tScov\tE-value\tScore\tSubstrate_Group\tSpec_Substrate\tFam_Name\n";
		fwrite($this->handle,$head);
		return TRUE;
	}
	
	
}
new Genome($argv[3],$argv[4],$argv[5]);
?>
