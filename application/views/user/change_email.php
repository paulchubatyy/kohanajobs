<h1>Change Email</h1>

<p>
	Your current email is:
	<strong><?php echo HTML::email(Auth::instance()->get_user()->email) ?></strong>.
</p>

<?php echo Form::open() ?>

	<?php echo Kohana::debug($errors) ?>

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