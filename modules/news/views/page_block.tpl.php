<div class="news-page-block news-page-block-<?php echo $display ?>">
	<?php foreach ($news as $item): ?>
		<article class="news-page-block-item card">
			<?php $item['introduction'] = bbcode($item['introduction']); ?>
			<?php echo $this->view('index', $item) ?>
		</article>
	<?php endforeach ?>
	<?php if (!$news): ?>
		<p class="news-page-block-empty"><?php echo $this->lang('There are no news published at the moment') ?></p>
	<?php endif ?>
</div>
