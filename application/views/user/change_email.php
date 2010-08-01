<h1>Change Email</h1>

<p>
	<?php if (empty($user->email)) { ?>
		No email has been stored for your account yet.
	<?php } else { ?>
		Your current email is <strong><?php echo HTML::email($user->email) ?></strong>.
	<?php } ?>
</p>

<?php echo Form::open() ?>

	<?php include Kohana::find_file('views', 'partials/errors') ?>

	<?php if ( ! Auth::instance()->logged_in_oauth()) { ?>
		<p>
			<?php echo Form::label('password', 'Password:') ?>
			<?php echo Form::password('password', NULL, array('id' => 'password')) ?>
		</p>
	<?php } ?>

	<p>
		<?php echo Form::label('email', 'New email:') ?>
		<?php echo Form::input('email', $post['email'], array('id' => 'email', 'type' => 'email')) ?>
	</p>

	<p>
		<?php echo Form::submit(NULL, 'Change email') ?>
	</p>

<?php echo Form::close() ?>