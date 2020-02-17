<?php

//Getting Post Values from HTML Form
$evalue = $_POST["evalue"];
$percent = $_POST["percent"];
//$email = $_POST["email"];

$allowed =  array('fasta','FASTA','fsa','FSA','faa','FAA');

$fileName = $_FILES["file1"]["name"]; // The file name
$fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["file1"]["type"]; // The type of file it is
$fileSize = $_FILES["file1"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true
$ext = pathinfo($fileName, PATHINFO_EXTENSION);

if (empty($_POST["evalue"])) {
echo "ERROR: E-value is required </br>";
}
  
if (empty($_POST["percent"])) {
echo "ERROR: Percent Alignment is required </br>";
}

if (empty($_POST["email"])) {
    echo "ERROR: Email is required </br>";
	exit();
  } else {
    $email = $_POST["email"];
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "ERROR: Invalid email format </br>";
		exit(); 
    }
}

if (!$fileTmpLoc) { // if file not chosen
    echo "ERROR: Please browse for an amino acid FASTA file before clicking the upload button.";
    exit();
}

if(!in_array($ext,$allowed) ) {
    echo "ERROR: Only amino acid FASTA file is allowed.";
	exit();
}

//Creating folders based on email address
$path = 'uploads/'. $email;

if ( ! is_dir($path)) {
    mkdir($path);
	chmod($path,0755);
}

if(move_uploaded_file($fileTmpLoc, "$path/$fileName")){
    echo "Blasting of <strong>{$fileName}</strong> file  was completed and please view your online results " . "[<a href='http://transath.umt.edu.my/readtsv.php?id=$email&file=$fileName' > <strong>HERE</strong> </a>]";
	//header("Location: http://transath.umt.edu.my/readtsv.php?id={$email}");
	/*
	ob_start();
	passthru($perlscript_file);
	$perlreturn = ob_get_contents();
	ob_end_clean();*/
	$folder = $path.'/'.'result';

	if ( ! is_dir($folder)) {
	    mkdir($folder);
		chmod($folder,0755);
	}

	$cmd = './gblast.pl uploads/'.$email.'/'.$fileName.' '.$email.' '.$evalue.' '.$percent;
	//echo '<pre>';
	exec($cmd);
	//echo '</pre>';

	/*while (@ ob_end_flush()); // end all output buffers if any

	$proc = popen($cmd, 'r');
	echo '<pre>';
	while (!feof($proc))
	{
	    echo fread($proc, 4096);
	    @ flush();
	}
	echo '</pre>';*/


} else {
    echo "move_uploaded_file function failed";
}

?>
