<?php echo $header?>
<div class="head">
<h3>Referrers</h3>
<p>Websites that send traffic to your website</p>
</div>
<?php
	foreach ($referrers as $referrer){
		echo'<div class="mini bloc">
		<h5><img src="https://www.google.com/s2/favicons?domain='.$referrer->referrer.'"> '.$referrer->referrer.'</h5>
		<p><i class="icon-eye"></i> '.$referrer->visits.'</p>
		</div>';
	}
?>
</div>
<?php echo $footer?>