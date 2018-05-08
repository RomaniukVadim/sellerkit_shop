<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="coupons"><i class="icon-arrow-left"></i></a>Add new coupon</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Coupon code</label>
							<input name="code" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Coupon discount</label>
							<input name="discount" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Coupon type</label>
							<select name="type" class="form-control" required>
							<option value="%">%</option>
							<option value="$">$</option>
							</select>
						  </div>
						  <input name="add" type="submit" value="Add coupon" class="btn btn-primary" />
					</fieldset>
				</form>';
	}
	elseif(isset($_GET['edit']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="coupons"><i class="icon-arrow-left"></i></a>Edit coupon</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Coupon code</label>
							<input name="code" value="'.$coupon->code.'" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Coupon discount</label>
							<input name="discount" value="'.$coupon->discount.'" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Coupon type</label>
							<select name="type" class="form-control" required>
							<option value="%">%</option>
							<option value="$">$</option>
							</select>
						  </div>
						  <input name="edit" type="submit" value="Edit coupon" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
?>
<div class="head">
<h3>Coupons<a href="coupons?add" class="add">Add coupon</a></h3>
<p>Manage your promotional coupons & discounts</p>
</div>
<?php
		echo $notices;
		foreach ($coupons as $coupon){
			echo '<div class="mini bloc">
			<h5>'.$coupon->code.' ('.$coupon->discount.$coupon->type.')
				<div class="tools">
					<a href="coupons?delete='.$coupon->id.'"><i class="icon-trash"></i></a>
					<a href="coupons?edit='.$coupon->id.'"><i class="icon-pencil"></i></a>
				</div>
			</h5>
			</div>';
		}
	}
?>