<?php echo $header?>
<div class="container landing-cover">
	<div class="post-meta">
		<h1 class="white"><?=translate($post->title) ?></h1>
		<div class="white"><i class="icon-clock"></i> Posted <?=timegap($post->time) ?> ago - <i class="icon-eye"></i> <?=$post->visits ?> <?=translate('Views') ?></div>
	</div>
</div>

</div>
<div class="container blog-post">
	<div class="content">
		<?=nl2br(translate($post->content)) ?>
		<div class="clearfix"></div>
	</div>
	<?php
	foreach ($blocs as $bloc){
		if (mb_substr($bloc->content, 0, 7) == 'widget:') {
			echo $__env->make('widgets/'.mb_substr($bloc->content, 7, 255))->render();
		} else {
			echo $bloc->content;
		}
	}
	?>
</div>
<?php echo $footer?>