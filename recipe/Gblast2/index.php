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

	<!-- CSS Implementing Plugins -->
	<link rel="stylesheet" href="assets/plugins/animate.css">
	<link rel="stylesheet" href="assets/plugins/line-icons/line-icons.css">
	<link rel="stylesheet" href="assets/plugins/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/plugins/sky-forms-pro/skyforms/css/sky-forms.css">
	<link rel="stylesheet" href="assets/plugins/sky-forms-pro/skyforms/custom/custom-sky-forms.css">
	<!--[if lt IE 9]><link rel="stylesheet" href="assets/plugins/sky-forms-pro/skyforms/css/sky-forms-ie8.css"><![endif]-->

	<!-- CSS Theme -->
	<link rel="stylesheet" href="assets/css/theme-colors/blue.css" id="style_color">
	<link rel="stylesheet" href="assets/css/theme-skins/dark.css">

	<!-- CSS Customization -->
	<link rel="stylesheet" href="assets/css/custom.css">
	
<script>

function _(el){
	return document.getElementById(el);
}
function uploadFile(){
	var file = _("file1").files[0];
	// alert(file.name+" | "+file.size+" | "+file.type);
	var formdata = new FormData();
	formdata.append("file1", file);
	formdata.append("evalue",document.getElementById('evalue').value);
	formdata.append("percent",document.getElementById('percent').value);
	formdata.append("email",document.getElementById('email').value);
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "actions.php");
	ajax.send(formdata);

}
function progressHandler(event){
	_("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
	var percent = (event.loaded / event.total) * 100;
	//_("progressBar").value = Math.round(percent);
	_("status").innerHTML = Math.round(percent)+"% uploaded and blasting results, <u><strong>DO NOT</strong></u> close this browser window, please wait..";
}
function completeHandler(event){
	_("status").innerHTML = event.target.responseText;
	_("progressBar").value = 0;
}
function errorHandler(event){
	_("status").innerHTML = "Upload Failed. Your input file size might be greater than the maximum allowed. Please use smaller file size for each upload.";
}
function abortHandler(event){
	_("status").innerHTML = "Upload Aborted";
}
</script>	
</head>

<body>
	<div class="wrapper">
		
		<!--=== Breadcrumbs ===-->
		<div class="breadcrumbs">
			<div class="container">
				<h1 class="pull-left">TransATH - Transporters via ATH (Annotation Transfer by Homology)</h1>
				<ul class="pull-right breadcrumb">
					<li><a href="#">Home</a></li>
					<li class="active">TransATH</li>
				</ul>
			</div>
		</div><!--/breadcrumbs-->
		<!--=== End Breadcrumbs ===-->

		<!--=== Content Part ===-->
		<div class="container content">
			<div class="row">

				<!-- Begin Content -->
				<div class="col-md-8 col-md-offset-2">

					<div class="margin-bottom-10"></div>

					<!-- TransATH Form -->
					<form action="actions.php" method="post" enctype="multipart/form-data" id="sky-form1" class="sky-form">
						<header>TransATH is an integrated program consists of sequence similiarity analysis (BLAST) and Hidden Markov Models (HMMs) to predict membrane transport proteins. It is an enhancement of G-BLAST program by Saier's lab.</header>
						<fieldset>

							<div id="status" class="alert alert-danger">* Please fill in <u><b>All</b></u> fields</div>	

							<section>
								<label class="input">
									<i class="icon-append fa fa-envelope"></i>
									<input type="email" name="email" id="email" placeholder="Your e-mail">
								</label>
							</section>
								
							<div class="row">								
								<section class="col col-6">
                                                                        <strong>Percent Identity:</strong>
									<label class="select">
										<select name="percent" id="percent" >
											<option value="0" selected disabled>Please Select</option>
											<option value="30">&gt; 30</option>
											<option value="40">&gt; 40</option>
											<option value="50">&gt; 50</option>
											<option value="60">&gt; 60</option>
											<option value="70">&gt; 70</option>
										</select>
										<i></i>
									</label>
								</section>
								<section class="col col-6">
                                                                        <strong>E-value:</strong>
									<label class="select">
										<select name="evalue" id="evalue" >
											<option value="0" selected disabled>Please Select</option>
											<option value="0.00000000000000000000000000000000000000000000000001">e-50</option>
											<option value="0.000000000000000000000000000001">e-30</option>											
											<option value="0.00000000000000000001">e-20</option>
											<option value="0.0000000001">e-10</option>
											<option value="0.00001">e-5</option>
											<option value="0.1">10</option>
										</select>
										<i></i>
									</label>
								</section>
							</div>

							<section>
								<label for="file" class="input input-file">
									<input type="file" name="file1" id="file1">
									
								</label>
							</section>

						</fieldset>

						<footer>
							<div class="row">
							<!--<button type="submit" class="btn-u">Submit Data &amp; Upload</button>-->
							<section class="col col-4">
								<input type="button" class="btn-u" value="Submit Data & Upload" onclick="uploadFile()">	
							</section>
  								<!--<progress id="progressBar" value="0" max="100" style="width:300px;"></progress></section>-->
  							</div>
  							<p id="loaded_n_total"></p>
						</footer>

					</form>
					<!-- End TransATH Form -->

					<div class="margin-bottom-100"></div>
				</div>
				<!-- End Content -->
			</div>
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
	
	<!-- JS Global Compulsory -->
	<script type="text/javascript" src="assets/plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="assets/plugins/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!-- JS Implementing Plugins -->
	<script type="text/javascript" src="assets/plugins/back-to-top.js"></script>
	<script type="text/javascript" src="assets/plugins/smoothScroll.js"></script>
	<script src="assets/plugins/sky-forms-pro/skyforms/js/jquery.validate.min.js"></script>
	<script src="assets/plugins/sky-forms-pro/skyforms/js/jquery.maskedinput.min.js"></script>
	<script src="assets/plugins/sky-forms-pro/skyforms/js/jquery-ui.min.js"></script>
	<script src="assets/plugins/sky-forms-pro/skyforms/js/jquery.form.min.js"></script>
	<!-- JS Customization -->
	<script type="text/javascript" src="assets/js/custom.js"></script>
	<!-- JS Page Level -->
	<script type="text/javascript" src="assets/js/app.js"></script>
	<!--[if lt IE 9]>
		<script src="assets/plugins/respond.js"></script>
		<script src="assets/plugins/html5shiv.js"></script>
		<script src="assets/plugins/placeholder-IE-fixes.js"></script>
		<script src="assets/plugins/sky-forms-pro/skyforms/js/sky-forms-ie8.js"></script>
		<![endif]-->
	
	<!--[if lt IE 10]>
		<script src="assets/plugins/sky-forms/version-2.0.1/js/jquery.placeholder.min.js"></script>
		<![endif]-->

</body>
</html>
