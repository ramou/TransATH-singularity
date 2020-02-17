<?php
header('Content-Encoding: none;');

//HTML5 Server-Sent Events Example

//header('Content-Type: text/event-stream');
//header('Cache-Control: no-cache');

//$time = date('r');
//echo "data: The server time is: {$time}\n\n";
//flush();

set_time_limit(0);
	//$cmd = './gblast.pl A_niger_CBS_513_88_current_orf_trans_all.fasta';
        $handle = popen("./gblast.pl AnigerTest2", "r");

        if (ob_get_level() == 0) 
            ob_start();

        while(!feof($handle)) {

            $buffer = fgets($handle);
            $buffer = trim(htmlspecialchars($buffer));

            echo $buffer . "<br />";
            echo str_pad('', 4096);    

            ob_flush();
            flush();
            sleep(1);
        }

        pclose($handle);
        ob_end_flush();
	//echo '<pre>';
	//system($cmd);
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

?> 
