<?php
//load the function
require_once 'tsv-to-array.inc.php';

$email = $_GET['id'];
$tsv = $_GET['file']; 
$file = "uploads/{$email}/result/clean.tsv";
$clean = "uploads/{$email}/result/clean.tsv";

//open up your file and convert it to an array
$data = tsv_to_array($file,array('header_row'=>true,'remove_header_row'=>true));

$path = '/var/www/html/Gblast2/uploads/'.$email.'/';

if ($handle = opendir($path)) {
    $files=array();
    while (false !== ($file = readdir($handle))) {
        if(is_file($file)){
            $files[]=$file;
        }
    }
    closedir($handle);
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
	<title>TransATH - Transporters via ATH (Annotation Transfer by Homology)</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Favicon -->
	<link rel="shortcut icon" href="favicon.ico">

	<!-- Web Fonts -->
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600&amp;subset=cyrillic,latin">

	<!-- CSS Global Compulsory -->
	<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/style.css">

	<!-- CSS Header and Footer -->
	<link rel="stylesheet" href="assets/css/headers/header-default.css">
	<link rel="stylesheet" href="assets/css/footers/footer-v1.css">	
		
	<link rel="stylesheet" href="assets/plugins/animate.css">
	<link rel="stylesheet" href="assets/plugins/line-icons/line-icons.css">
	<link rel="stylesheet" href="assets/plugins/font-awesome/css/font-awesome.min.css">

	<!-- CSS Theme -->
	<link rel="stylesheet" href="assets/css/theme-colors/blue.css" id="style_color">

	<!-- CSS Customization -->
	<link rel="stylesheet" href="assets/css/custom.css">
	<link rel="stylesheet" href="assets/css/jquery-ui.css">
	<link rel="stylesheet" href="assets/css/dataTables.jqueryui.min.css">

	
	</script>	
</head>

<body>
	<div class="wrapper">
		
		<!--=== Breadcrumbs ===-->
		<div class="breadcrumbs">
			<div class="container">
				<h1 class="pull-left">TransATH - Transporters via ATH (Annotation Transfer by Homology)</h1>
				<ul class="pull-right breadcrumb">
					<li><a href="http://transath.umt.edu.my/"target="_blank">Home</a></li>
					<li class="active">Results</li>
				</ul>
			</div>
		</div><!--/breadcrumbs-->
		<!--=== End Breadcrumbs ===-->

		<!--=== Content Part ===-->
		<div class="container content">
			
			<div class="row">
			<button class="btn-u pull-right margin-left-10" data-toggle="modal" data-target=".bs-example-modal-lg"><i class="fa fa-pie-chart"></i> View Chart</button>
			<button class="btn-u pull-right" onclick="window.location.href='<?php echo $clean ?>'"><i class="fa fa-file-text"></i></button>
				<!-- Begin Content -->
				<div class="col-md-12">

					<div class="margin-bottom-10"></div>
			
						<h4>Predicted Transporters for <strong><?php echo "{$tsv}"; ?></strong></h4>
						<table id="result" class="display" cellspacing="0" width="100%">
				        <thead>
				            <tr>
				                <th class="no-sort">Family TC#</th>
				                <th>Family Name</th>
				                <th>Hit TCID</th>
				                <th>Acc.in TCDB</th>
				                <th>Hit TMS#</th>
				                <th>Substrate Group</th>
				                <th>Specific Substrate</th>
				                <th>Sequence ID#</th>
				                <th>Query TMS#</th>
				            </tr>
				        </thead>
				        
				        <tbody>
				        		<?php 
								$result = array();
								
				        		foreach ($data as $title => $value){?>
				            <tr>
				                <td><?php echo $value['Family_TC#'] ?></td>
				                <td><?php echo $value['Family_Name'] ?></td>
				                <td><?php echo $value['Hit_TCID'] ?></td>
				                <td><?php echo $value['Acc.in_TCDB'] ?></td>
				                <td><?php echo $value['Hit_TMS#'] ?></td>
				                <td><?php echo $value['Substrate_Group'] ?></td>
				                <td><?php echo $value['Spec_Substrate'] ?></td>
				                <td><?php echo $value['Seq_ID#'] ?></td>
				                <td><?php echo $value['Qry_TMS#'] ?></td>
				            </tr>
				             	<?php								
								
								$result[] = $value['Substrate_Group'];

								} 

								$dataset = @array_count_values($result);

								// Create Flot formatted DATA
								/*var data = [
							    { label: "Series1",  data: 10},
							    { label: "Series2",  data: 30},
							    { label: "Series3",  data: 90},
							    { label: "Series4",  data: 70},
							    { label: "Series5",  data: 80},
							    { label: "Series6",  data: 110}
							    ];*/
							    
							    //echo nl2br(print_r($dataset,TRUE));
							    
							    $format = "[";
								
								foreach ($dataset as $label => $data) {
									
									$format .= "{label: " . '"' . $label . '",' ."data: ". $data ."},";
									
								}
								
							    $format .= "]";
								
								?>
				         </tbody>       
				    	</table>
				    
				</div>
			</div>
			<div class="margin-bottom-30"></div>
			
			<!-- Large modal -->
			<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
							<h4 id="myLargeModalLabel2" class="modal-title"><b>Substrate Group Pie Chart</b></h4>
						</div>
						<div class="modal-body">
							<!-- Begin Content -->

							<div style="float:left">
					        Percentage Values : <span id="showInteractive"></span>
							</div>							
							<div id="placeholder" style="width:900px;height:700px;"></div>
						</div>
					</div>
				</div>
			</div>
			<!-- Large modal -->
		</div><!--/container-->
		<!--=== End Content Part ===-->

		<!--=== Footer Version 1 ===-->
		<div class="footer-v1">

			<div class="copyright">
				<div class="container">
					<div class="row">
						<div class="col-md-6">
							<p>
								&copy; faizah_aplop@umt.edu.my & gregb@encs.concordia.ca All Rights Reserved.
							</p>
						</div>						
					</div>
				</div>
			</div><!--/copyright-->
		</div>
		<!--=== End Footer Version 1 ===-->
	</div><!--/End Wrapper-->

	<!-- Datatables -->
	<script type="text/javascript" src="assets/js/jquery-1.12.0.min.js"></script>
	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	
	<script type="text/javascript" src="assets/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.flot.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.flot.pie.min.js"></script>
	<!--[if lt IE 9]>
		<script src="assets/plugins/respond.js"></script>
		<script src="assets/plugins/html5shiv.js"></script>
		<script src="assets/plugins/placeholder-IE-fixes.js"></script>
		<script src="assets/plugins/sky-forms-pro/skyforms/js/sky-forms-ie8.js"></script>
		<![endif]-->
	
	<!--[if lt IE 10]>
		<script src="assets/plugins/sky-forms/version-2.0.1/js/jquery.placeholder.min.js"></script>
		<![endif]-->
		<script>
		
			$(document).ready(function() {
		    $('#result').dataTable( {
				    "order": [],
				    "columnDefs": [ {
				      "targets"  : 'no-sort',
				      "orderable": false,
				    }]
				});
			});
			
			var dataset = <?php echo $format; ?>;

		    var options = {
		            series: {
		                pie: {
		                    show: true,
		                    radius: 1,
		                    tilt: 0.9,
		                    label:{                        
		                        radius: 3/4,
		                        formatter: function (label, series) {
		                            return '<div style="font-size:8pt;text-align:center;color:#000000;">' + label + '<br/>' +   
		                            Math.round(series.percent) + '%</div>';
		                        },
		                        background: {
		                            opacity: 0.5,
		                            color: '#000000'
		                        }
		                    }
		                }
		                    },
		            legend: {
		                show: false
		            },
		            grid: {
		                hoverable: true,
		                clickable: true
		            }
		         };
 
    			$.plot($("#placeholder"), dataset, options);  
 
			    $("#placeholder").bind("plothover", function(event, pos, obj){
			        if (!obj){return;}
			            percent = parseFloat(obj.series.percent).toFixed(0);
			 
			        var html = [];
			        html.push("<div style=\"flot:left;width:200px;height:40px;text-align:center;border:1px solid black;background-color:", obj.series.color, "\">",
			                  "<span style=\"font-weight:bold;color:#000000\">", obj.series.label, " (", percent, "%)</span>",
			                  "</div>");
			 
			            $("#showInteractive").html(html.join(''));        

				});

		</script>
</body>
</html>
