<?php echo $header?>
	<div class="bheader bg">
		<h2><?=translate('Account')?></h2>
	</div>
	<div class="col-md-4 account">
		<h6><i class="icon-list"></i> <?=translate('Order History')?></h6>
		<?php foreach ($orders as $order){ ?>
		<a class="customer-order smooth" href="<?=url('invoice/'.$order->id)?>" data-title="Invoice">
			<h6><b><?='#'.$order->id.' - '.$order->name?></b><b class="total"><?=c($order->summ)?></b></h6>
		</a>
		<?php } if (count($orders) == 0) {?>
		<div class="no-orders">
			<i class="icon-basket"></i>
			<?=translate('You have not made any previous orders!')?>
		</div>
		<?php }?>
	</div>
	<div class="clearfix"></div>
<?php echo $footer?>