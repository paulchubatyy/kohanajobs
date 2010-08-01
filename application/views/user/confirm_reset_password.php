<h1>Choose a new password</h1>

<?php echo Form::open() ?>

	<?php include Kohana::find_file('views', 'partials/errors') ?>

	<p>
		<?php echo Form::label('password', 'New password:') ?>
		<?php echo Form::password('password', NULL, array('id' => 'password')) ?>
	</p>

	<p>
		<?php echo Form::label('password_confirm', 'New password again:') ?>
		<?php echo Form::password('password_confirm', NULL, array('id' => 'password_confirm')) ?>
	</p>

	<p>
		<?php echo Form::submit(NULL, 'Apply password and go to signin form') ?>
	</p>

<?php echo Form::close() ?>