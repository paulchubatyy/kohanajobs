<h1><?php echo $total_jobs, ' ', Inflector::plural('job', $total_jobs) ?> available</h1>

<ul>
	<?php foreach ($jobs as $job) { ?>
		<li>
			<?php echo HTML::anchor(Route::get('job')->uri(array('id' => $job->id)), HTML::chars($job->title)) ?>
		</li>
	<?php } ?>
</ul>

<?php echo $pagination ?>