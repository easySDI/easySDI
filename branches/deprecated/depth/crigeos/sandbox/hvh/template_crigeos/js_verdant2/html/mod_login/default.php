<?php // @version $Id: default.php 11796 2009-05-06 02:03:15Z ian $
defined('_JEXEC') or die('Restricted access');
?>
<?php
$return = base64_encode(base64_decode($return).'#content');
if ($type == 'logout') : ?>
<form action="index.php" method="post" name="login" id="login-form" class="log">
	<div class="form-login">
		<?php if ($params->get('greeting')) : ?>
			<div class="part">
			<?php if ($params->get('name')) : {
				echo JText::sprintf( 'HINAME', $user->get('name') );
			} else : {
				echo JText::sprintf( 'HINAME', $user->get('username') );
			} endif; ?>
			</div>
		<?php endif; ?>	
		<button value="submit" class="submitBtn"><span><span>Logout</span></span></button>	
		<input type="hidden" name="option" value="com_user" />
		<input type="hidden" name="task" value="logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
	</div>
</form>
<?php else : ?>
<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="login-form">
	<div class="form-login">
		<?php if ($params->get('pretext')) : ?>
		<div class="part">
			<?php echo $params->get('pretext'); ?>
		</div>
		<?php endif; ?>
		
		<div class="part">
			<label for="mod_login_username">Username</label>
			<input name="username" id="mod_login_username" type="text" class="text" alt="<?php echo JText::_('Username'); ?>" />
		</div>
		
		<div class="part">
			<label for="mod_login_password">Password</label>
			<input type="password" id="mod_login_password" name="passwd" class="text"  alt="<?php echo JText::_('Password'); ?>" />
		</div>
		
		<div class="remember">
			<div>
				<input type="checkbox" name="remember" id="mod_login_remember" class="check" value="yes" alt="<?php echo JText::_('Remember me'); ?>" />
				<label class="label" for="mod_login_remember">Remember Me</label>
			</div>
			<button value="submit" class="submitBtn"><span><span>Login</span></span></button>
		</div>	
	<ul>
		<li>
			<a class="forgotpass" href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>">
			<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a class="forgotuser" href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>">
			<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a class="regusr" href="<?php echo JRoute::_( 'index.php?option=com_user&task=register' ); ?>">
				<?php echo JText::_('REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>	
		<?php echo $params->get('posttext'); ?>
		<input type="hidden" name="option" value="com_user" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>
<?php endif; ?>
