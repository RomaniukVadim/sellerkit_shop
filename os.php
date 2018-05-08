<?php echo $header?>
<div class="head">
<h3>Operating systems</h3>
<p>Operating systems used by your customers</p>
</div>
<?php
	foreach ($OSs as $os){
		echo'<div class="mini bloc">
		<h5>'.$os->os.'</h5>
		<p><i class="icon-eye"></i> '.$os->visits.'</p>
		</div>';
	}
?>
</div>
<?php echo $footer?>