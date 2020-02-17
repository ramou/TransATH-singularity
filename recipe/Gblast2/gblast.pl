#!/usr/bin/perl -w

use Bio::SearchIO;

my $workpath = "/var/www/html/Gblast2/";
my $term = $/;
my $qfile = $ARGV[0];
my $email = "uploads/".$ARGV[1]."/";
my $percent = $ARGV[2];
my $evalue = $ARGV[3];
my $sfile = $workpath . 'db/tcdb';
my $cfile = $workpath . 'subcat';
my $blst = $workpath . $email .'result/blast.out';
$hitsout = $workpath . $email .'result/hits.out';

our ($id, $sequence, $line, $qry_tms, $pfam_url);
our %qhash=();
our %shash=();
our (@all_qry_hits, @all_qry, @all_subj, @sequencelines);

open(QFASTA,"<",$qfile) or die("Open failed: $!");
$/ = ">";

print "Mapping IDs to Seqs...";
while(<QFASTA>){
	chomp;
    next if($_ eq '');
    ($id, @sequencelines) = split /\n/;
	if ($id =~ /^gi\|(\d+)\|(.+)/) { 
		$id = $1;
		push(@all_qry, $id);
    	$sequence = '';
    	foreach $line (@sequencelines) {
       		$sequence .= $line;
    	}
    	$qhash{$id} = $sequence;
	}
}
$/ = $term;

open(SFASTA,"<",$sfile) or die("Open failed: $!");
$/ = ">";
while(<SFASTA>){
	chomp;
    next if($_ eq '');
    ($id, @sequencelines) = split /\n/;
	if ($id =~ /^gnl\|TC-DB\|(\S+)\s+(.+)/) { 
		$id = $1;
		push(@all_subj, $id);
    	$sequence = '';
    	foreach $line (@sequencelines) {
       		$sequence .= $line;
    	}
    	$shash{$id} = $sequence;
	}
}
$/ = $term;
print "DONE.\n";

print "BLASTing...\n";
# here $sfile = tcdb (as the formated DB) file # $qfile = query (sequence of interest) file # $blst = BLAST output file 
system("blastall -p blastp -d $sfile -i $qfile -e $evalue -o $blst"); #-m 7 
#system("blastall -p blastp -d $sfile -i $qfile -m 9 -B 3 -b 10 -e 0.00000000000000000001 -o $blst"); tabular result
print "Now parsing and stuff... (this might take a while)\n";
# read blast output file
my $blast_report = new Bio::SearchIO ('-format' => 'blast', '-file' => $workpath . $email . 'result/blast.out', -best_hit_only =>'true'); #-signif => '1e-5'

open (HITSOUT, ">$hitsout") or die "couldn't open $hitsout, $!\n";
print HITSOUT "Query\tHit\tTCID\tQry_TMS\tHit_TMS\tPer_Diff\tPer_identity.\tQcov\tScov\tE-value\tScore\tSub_group\tSpec_subs\n";

#Original hits.out
#print HITSOUT "Query\tHit\tTCID\tQry_Description\tHit_Description\tQry_TMS\tHit_TMS\tQry_Length\tSub_Length\thsp_hit-length\thsp_total-length\tQry_Region\tHit_Region\tPer_identity.\tQcov\tScov\tE-value\tScore\tSub_group\tspec_subs\n";


sub topology {
	my $seq = shift(@_);
	my $hmmtop = `/var/www/html/Gblast2/hmmtop -if=$seq`;
	if($hmmtop =~ /^\>(.+)\s+(IN|OUT)\s+(\S+)/){ 
		my $tms = $3; 
		return $tms;
	}	
}

# get next result and cycle
while( my $result = $blast_report->next_result) {
	
	#$cnt = 0;
	# get next hit and cycle
	while( my $hit = $result->next_hit()) {   
		
		#while ($cnt < 5) { # just one loop to print out the top hits only
			# get next HSP and cycle
			while( my $hsp = $hit->next_hsp()) {  
				$qry = $result->query_name();
				$qry_desc = $result->query_description();
				$hitdescs = $hit->description();
				my $tc_acc = $hit->accession;
				#$percent_align = sprintf("%.2f", ($hsp->length('hit')/$hit->length)*100); #calculation for % alignment
				$percent = sprintf("%.2f", $hsp->percent_identity);    #percent identity  
				$Diff = sprintf("%.2f", ($hit->length>$result->query_length) ? (($hit->length-$result->query_length)/$hit->length)*100 : 
				(($result->query_length-$hit->length)/$result->query_length)*100);		
				                        
				$Qcov = sprintf("%.2f", ($hsp->length('total')/$result->query_length)*100); #calculation for query coverage
				$Scov = sprintf("%.2f", ($hsp->length('total')/$hit->length)*100); #calculation for subject coverage				
				if ($qry_desc =~ /^(.+)\[.+\]/) { $qry_desc = $1; }
				if ($hitdescs =~ /^(\S+)\s+(.+)/) { $tcid = $1; $hit_desc = $2; }
				if ($qry =~ /^gi\|(\d+)\|(.+)/) { $qry = $1; push(@all_qry_hits, $qry); }
				
				my %h;
				@h{@all_qry_hits} = @all_qry_hits;
				@all_qry_hits = values %h;
				
				#get the query sequence and save it in a temp file
				$qry_seq = '>'.$qry."|".$qry_desc."\n".$qhash{$qry};
				$temp_query = $email.'result/temp_qry_faa';
				open (QTEMP, ">$temp_query") or die "couldn't open $temp_query, $!\n";
				print QTEMP $qry_seq;
				
				#check query topology		#topology("result/temp_qry_faa");
				
				#get the subject sequence and save it in a temp file
				$subj_seq = '>'.$hitdescs."\n".$shash{$tc_acc};
				$temp_subject = $email.'result/temp_subj_faa';
				open (STEMP, ">$temp_subject") or die "couldn't open $temp_query, $!\n";
				print STEMP $subj_seq;
				
				open(INFILE,$cfile) or die;
				chomp (@lines = (<INFILE>));
				close(INFILE);
				$i = 0;
				$subg='';
				$subs='';				
				foreach $line (@lines) {
   				($tid, $subg, $subs) = split("\t", $line);
				if ($tcid eq $tid){
					$subg = $subg;
					$subs = $subs;
					last;				
				#print $subs;
				}
				$i++;
				}
								# print query info, hit info, HSP info
				print HITSOUT
					$qry,"\t",$hit->accession,"\t",$tcid,"\t", 
					topology($email."result/temp_qry_faa"),"\t",topology($email."result/temp_subj_faa"),"\t", $Diff,"\t",
					$percent,"%\t",$Qcov,"\t",$Scov,"\t",  $hsp->evalue(),"\t",$hsp->score(),"\t",$subg,"\t",$subs,"\n";
					
					#topology("result/temp_qry_faa"),
					#Original hits.out
					#$qry,"\t",$hit->accession,"\t",$tcid,"\t",$qry_desc,"\t",$hit_desc,"\t",
					#topology("result/temp_qry_faa"),"\t",topology("result/temp_subj_faa"),"\t",$result->query_length,"\t",$hit->length,"\t",
					#$hsp->start('query')," ~ ",$hsp->end('query'),"\t",$hsp->start('hit')," ~ ",$hsp->end('hit'),"\t",
					#$percent,"%\t",$hsp->length('hit'),"\t",$hsp->length('total'),"\t",$Qcov,"\t",$Scov,"\t",  $hsp->evalue(),"\t",$hsp->score(),"\t",$subg,"\t",$subs,"\n"; 
	        }
			# end-of-line
	        print "\n";
			#$cnt++;
		}
		next;
	}
#}

$no_hits = $workpath . $email . 'result/no_hits.out';
open (NOHITS, ">$no_hits") or die "couldn't open $no_hits, $!\n";

map $count{$_}++ , @all_qry, @all_qry_hits;
$, = "\t";
push(@nohits, grep $count{$_} == 1, @all_qry, @all_qry_hits);

foreach $nohit (@nohits) {
	$sequence = $qhash{$nohit};
	print NOHITS '>'.$nohit."\n".$sequence,"\n";
}

$cmd = $workpath . 'gblast2.php' .' '. $evalue . ' ' . $email;
$cleanfile = $workpath . $email .'./result/clean.tsv';
system("$cmd $hitsout $qfile $cleanfile");

unlink ($workpath . $email ."result/temp_subj_faa");
unlink ($workpath . $email . "result/temp_qry_faa");
print "\n******* All Done! *******\n";
print "The result is in: result/clean.tsv\n";
print "blast.out is just the BLAST output\n";
print "Sequences with 'No hits to TCDB' are saved in ",$no_hits,"\n";

exit 0;
