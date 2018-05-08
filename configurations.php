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
			<form action="" method="POST">
				<h5>Site Configuration</h5>
				<?=csrf_field()?>
				<div class="form-group">
					<label class="control-label">Site name</label>
					<input name="sname" class="form-control" type="text" required>
				</div>
				<div class="form-group">
					<label class="control-label">Site description</label>
					<textarea name="desc" class="form-control" type="text" required></textarea>
				</div>
				<div class="form-group">
					<label class="control-label">Site keywords</label>
					<textarea name="key" class="form-control" type="text" required></textarea>
				</div>
				<div class="form-group">
					<label class="control-label">Site URL</label>
					<input name="url" class="form-control" type="text" required value="<?php echo url('')?>">
				</div>
				<div class="form-group">
					<label class="control-label">Site email</label>
					<input name="semail" class="form-control" type="text" required>
				</div>
				<div class="form-group">
					<label class="control-label">Admin name</label>
					<input name="name" class="form-control" type="text" required>
				</div>
				<div class="form-group">
					<label class="control-label">Admin email</label>
					<input name="email" class="form-control" type="text" required>
				</div>
				<div class="form-group">
					<label class="control-label">Admin password</label>
					<input name="password" class="form-control" type="password" required>
				</div>
				<input class="btn btn-primary" value="Install" name="install" type="submit">
			</form>
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