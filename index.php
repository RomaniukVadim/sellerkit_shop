<?php echo $header?>
<div class="container landing-cover">
	<div class="col-md-6 no-padding">
		<h1 class="landing-heading"><?=translate($style->slogan) ?></h1>
		<p class="landing-details"><?=translate($style->desc) ?></p>
		<a class="landing-cta smooth c" href="<?=explode(',',$style->button)[1] ?>" data-title="Products"><?=translate(explode(',',$style->button)[0]) ?></a>
	</div>
	<div class="landing-media col-md-6 no-padding">
		<?=$media ?>
	</div>
</div>
</div>
<?php
	foreach ($blocs as $bloc){
		if (mb_substr($bloc->content, 0, 7) == 'widget:') {
			echo $__env->make('widgets/'.mb_substr($bloc->content, 7, 255))->render();
		} else {
			echo $bloc->content;
		}
	}
echo $footer
?>