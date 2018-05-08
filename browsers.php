<?php echo $header?>
<div class="head">
<h3>Browsers</h3>
<p>Browsers used by your customers</p>
</div>
<?php
	foreach ($browsers as $browser){
		echo'<div class="mini bloc">
		<h5>'.$browser->browser.'</h5>
		<p><i class="icon-eye"></i> '.$browser->visits.'</p>
		</div>';
	}
?>
</div>
<?php echo $footer?>