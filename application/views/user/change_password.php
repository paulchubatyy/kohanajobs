<h1>Change Password</h1>

<?php echo Form::open() ?>

	<?php include Kohana::find_file('views', 'partials/errors') ?>

	<p>
		<?php echo Form::label('old_password', 'Current password:') ?>
		<?php echo Form::password('old_password', NULL, array('id' => 'old_password')) ?>
	</p>

	<p>
		<?php echo Form::label('password', 'New password:') ?>
		<?php echo Form::password('password', NULL, array('id' => 'password')) ?>
	</p>

	<p>
		<?php echo Form::label('password_confirm', 'New password again:') ?>
		<?php echo Form::password('password_confirm', NULL, array('id' => 'password_confirm')) ?>
	</p>

	<p>
		<?php echo Form::submit(NULL, 'Change password') ?>
	</p>

<?php echo Form::close() ?>