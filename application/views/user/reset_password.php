<h1>Reset password</h1>

<?php echo Form::open() ?>

	<?php echo Kohana::debug($errors) ?>

	<p>
		<?php echo Form::label('email', 'Email:') ?>
		<?php echo Form::input('email', $post['email'], array('id' => 'email', 'type' => 'email')) ?>
	</p>

	<p>
		<?php echo Form::submit(NULL, 'Reset password') ?>
	</p>

<?php echo Form::close() ?>