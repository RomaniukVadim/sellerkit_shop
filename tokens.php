<?php echo $header; ?>
	<div class="head">
		<h3>Tokens<a href="tokens?add" class="add">Generate a token</a></h3>
		<p>Access advanced API functions that requires an API token</p>
	</div>
<?php
	echo $notices;
	foreach ($tokens as $token){
		echo'<div class="mini bloc">
			<h5>
				'.$token->token.'
				<div class="tools">
					<a href="tokens?delete='.$token->token.'"><i class="icon-trash"></i></a>
				</div>
			</h5>
			<p>Total requests : '.$token->requests.'</p>
		</div>';
	}
	echo $footer;
?>