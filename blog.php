<div class="container blog" >
	<h4 class="pull-left"><?=translate('Blog posts');?></h4>
	<a href="blog" class="theme-btn pull-right smooth bg" data-title="<?=translate('Blog')?>" style="margin-top: 6px;"><?=translate('More posts')?> &#8594;</a>
	<div class="clearfix"></div>
	<div id="blog" class="row">
		<?php
			$posts = DB::select("SELECT * FROM blog ORDER BY time DESC LIMIT 3");
			foreach($posts as $post){
				echo '<div class="col-md-4">
					<a data-title="'.translate($post->title).'" class="smooth" href="blog/'.path($post->title,$post->id).'">
					<div class="post" id="'.$post->id.'">
					<div class="post-image" style="background-image:url(\''.url('/assets/blog/'.$post->images).'\')"></div>
						<h4>'.translate($post->title).'</h4>
						<div class="i">
							<div class="pull-left"><i class="icon-clock"></i> '.translate('Posted ').timegap($post->time).translate(' ago').'</div>
							<div class="pull-right"><i class="icon-eye"></i> '.$post->visits.' '.translate('Views').'</div>
							<div class="clearfix"></div>
						</div>
					</div>
					</a>
				</div>';
			}
		?>
		<div class="clearfix"></div>
	</div>
</div>