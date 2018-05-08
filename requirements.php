<!DOCTYPE html>
<html>
    <head>
		<meta charset='utf-8'/>
		<title>Installation - SellerKit</title>        
		<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<meta name="description" content="Shop installer script">
		<meta name="copyright" content="Shop" />
		<base href="<?=url('')?>/" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="themes/default/assets/style.css">
	</head>
	<body dir="ltr">
		<div class="installer-header">
			<h3>SellerKit Installer</h3>
		</div>
		<div class="col-md-4 installer-body">
			<h5>Reguirements check</h5>
			<?php 
			if (is_array($requirements)){
				foreach ($requirements as $requirement){
					echo '<p>'.$requirement.'</p>';
				}
				echo '<a href='.url('install/database').'><button class="btn btn-primary">Continue with installation anyway</button>';
			} else {
				echo '<p>All requirements are available :)</p>
				<a href='.url('install/database').'><button class="btn btn-primary">Continue with installation</button></a>';
			}
			?>
		</div>
		<style>
		.installer-header {
			background: linear-gradient(to right, #4c77c6,#649bf2) repeat scroll 0% 0%;
			padding: 70px 0px 100px 0px;
			text-align: center;
		}
		.installer-header h3 {
			margin: 0px;
			color: white;
		}
		.installer-body {
			float: none;
			margin: auto;
			background: white;
			margin-top: -30px;
			border-radius: 5px;
			padding: 10px 20px;
		}
		.btn  {
			display: table;
			border-radius: 50px;
			margin: 10px auto;
		}
		</style>
	</body>
</html>