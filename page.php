<?php echo $header?>
<div class="container">
	<h1 class="title"><?=translate($page->title) ?></h1>
	<div class="content">
	<?=nl2br(translate($page->content))?>
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