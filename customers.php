<?php echo $header; ?>
	<div class="head">
		<h3>Customers</h3>
		<p>Manage your customers accounts</p>
	</div>
<?php
	echo $notices;
	foreach ($customers as $customer){
		echo'<div class="mini bloc">
			<h5>
				'.$customer->name.'
				<div class="tools">
					<a href="customers?delete='.$customer->id.'"><i class="icon-trash"></i></a>
				</div>
			</h5>
			<p>'.$customer->email.'</p>
		</div>';
	}
	echo $footer;
?>