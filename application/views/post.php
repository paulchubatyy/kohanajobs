<?php echo $preview ?>

<form id="jobform" action="<?php echo URL::site(Route::get('post')->uri()) ?>#formerrors" method="post">

	<h1><?php echo __('Create a new job listing') ?></h1>

	<?php if ( ! empty($errors)): ?>
		<div id="formerrors" class="errorbox">
			<h3><?php echo __('Review the form') ?></h3>
			<ul>
				<?php foreach($errors as $field => $message): ?>
					<li>
						<label for="<?php echo $field ?>"><?php echo $message ?></label>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	<?php endif ?>

	<fieldset>
		<legend><?php echo __('About you') ?></legend>

		<p<?php if (isset($errors['company'])) echo ' class="error"' ?>>
			<label for="company"><?php echo __('Company name') ?><abbr title="required">*</abbr></label>
			<input id="company" name="company" type="text" value="<?php echo HTML::chars($job->company) ?>" maxlength="100" size="30" />
			<samp><?php echo __('Example: “idoe studios” (or your personal name)') ?></samp>
		</p>

		<p<?php if (isset($errors['location'])) echo ' class="error"' ?>>
			<label for="location"><?php echo __('Location') ?><abbr title="required">*</abbr></label>
			<input id="location" name="location" type="text" value="<?php echo HTML::chars($job->location) ?>" maxlength="100" size="30" />
			<samp><?php echo __('Example: “Miami, FL” or “Paris, France”') ?></samp>
		</p>

		<p<?php if (isset($errors['website'])) echo ' class="error"' ?>>
			<label for="website"><?php echo __('Website') ?></label>
			<input id="website" name="website" type="text" value="<?php echo HTML::chars($job->website) ?>" maxlength="100" size="30" />
			<samp><?php echo __('Example: “http://www.kohanaphp.com/”') ?></samp>
		</p>

		<p<?php if (isset($errors['email'])) echo ' class="error"' ?>>
			<label for="email"><?php echo __('E-mail') ?><abbr title="required">*</abbr></label>
			<input id="email" name="email" type="text" value="<?php echo HTML::chars($job->email) ?>" maxlength="100" size="30" />
			<samp><?php echo __('This e-mail address will not be made public. <br />We will just send you a confirmation link.') ?></samp>
		</p>
	</fieldset>

	<fieldset>
		<legend><?php echo __('About the job') ?></legend>

		<p<?php if (isset($errors['title'])) echo ' class="error"' ?>>
			<label for="title"><?php echo __('Job title') ?><abbr title="required">*</abbr></label>
			<input id="title" name="title" type="text" value="<?php echo HTML::chars($job->title) ?>" maxlength="100" size="50" />
			<samp><?php echo __('Example: “PHP/MySQL developer for e-commerce project”') ?></samp>
		</p>

		<p<?php if (isset($errors['description'])) echo ' class="error"' ?>>
			<label for="description"><?php echo __('Description') ?><abbr title="required">*</abbr></label>
			<textarea id="description" name="description" cols="50" rows="10"><?php echo HTML::chars($job->description) ?></textarea>
			<samp><?php echo __('No HTML allowed, only **bold text**') ?></samp>
		</p>

		<p<?php if (isset($errors['apply'])) echo ' class="error"' ?>>
			<label for="apply"><?php echo __('How to apply') ?><abbr title="required">*</abbr></label>
			<input id="apply" name="apply" type="text" value="<?php echo HTML::chars($job->apply) ?>" maxlength="200" size="50" />
			<samp><?php echo __('Example: “Send your portfolio to jobs@company.com”') ?></samp>
		</p>
	</fieldset>

	<fieldset>
		<legend><?php echo __('Submit') ?></legend>

		<p class="switch<?php if (isset($errors['terms'])) echo ' error' ?>">
			<input id="terms" name="terms" type="checkbox"  />
			<label for="terms"><?php echo __('I understand that my listing may be removed if it is for a position that involves adult content or an illegitimate work opportunity.') ?></label>
		</p>

		<p>
			<input class="main" type="submit" value="<?php echo __('Post') ?>" />
			or <input name="preview" type="submit" value="<?php echo __('Preview') ?>" />
		</p>
	</fieldset>

</form>