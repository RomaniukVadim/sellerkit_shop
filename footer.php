		</div>
	</div>
	<div id="footer">
		<div class="container">
			<ul class="links">
				<?php foreach ($links as $link) {?>
					<li><a href="<?=$link->link ?>" class="smooth"><?=translate($link->title) ?></a></li>
				<?php } ?>
			</ul>
			<ul class="links social">
				<?php foreach($social as $platform => $account) {?>
					<li><a href="<?=$account ?>"><i class="icon-social-<?=$platform ?>"></i></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	</body>
</html>