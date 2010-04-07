	<h1><?php echo $total_jobs, ' ', Inflector::plural('job', $total_jobs) ?> available</h1>

			<table summary="<?php echo __('List of all Kohana jobs (most recent first)') ?>">
				<thead>
					<tr>
						<th><?php echo __('Date') ?></th>
						<th><?php echo __('Job') ?></th>
					</tr>	
				</thead>
				<tbody>
<?php foreach ($jobs as $job) { ?>
					<tr>
						<td><?php echo date('d M', $job->created) ?></td>
						<td><strong><?php echo HTML::anchor(Route::get('job')->uri(array('id' => $job->id)), HTML::chars($job->title)) ?></strong><br />
						at <?php echo $job->company ?></td>
					</tr>
<?php } ?>
				</tbody>
			</table>

<?php echo $pagination ?>