<h1>Sign up</h1>

<?php echo Form::open() ?>

	<?php include Kohana::find_file('views', 'partials/errors') ?>

	<p>
		<?php echo Form::label('username', 'Your username:') ?>
		<?php echo Form::input('username', $post['username'], array('id' => 'username')) ?>
	</p>

	<p>
		<?php echo Form::label('email', 'Your e-mail:') ?>
		<?php echo Form::input('email', $post['email'], array('id' => 'email', 'type' => 'email')) ?>
	</p>

	<p>
		<?php echo Form::label('password', 'Your password:') ?>
		<?php echo Form::password('password', NULL, array('id' => 'password')) ?>
	</p>

	<p>
		<?php echo Form::label('password_confirm', 'Your password again:') ?>
		<?php echo Form::password('password_confirm', NULL, array('id' => 'password_confirm')) ?>
	</p>

	<p>
		<?php echo Form::submit(NULL, 'Sign up') ?>
	</p>

<?php echo Form::close() ?>