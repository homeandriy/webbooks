<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>

<div class=" main-section">
		<div class="container-fluid">
		<div class="row">			
			<div class=" center-block f_none col-md-6">
				<div class="panel panel-default mrg-t">
					<div class="panel-body bdr-b bg-brown-lighten">
						<h4>Войти  в аккаунт</h4>
						<p class="text-lighten">
							<?php $template->the_action_template_message( 'login' ); ?>
						<?php $template->the_errors(); ?>
						</p>
					</div>
					<div class="panel-body">
						
						<form name="loginform" id="loginform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'login' ); ?>" method="post">
							<div class="form-group">
								<label for="user_login<?php $template->the_instance(); ?>"><?php _e( 'Username', 'theme-my-login' ); ?><span class="text-red">*</span></label>
								<input type="text" name="log" id="user_login<?php $template->the_instance(); ?>" class="form-control input-lg" value="<?php $template->the_posted_value( 'log' ); ?>" size="20" />
							</div>
							<div class="form-group">
								<label for="user_pass<?php $template->the_instance(); ?>"><?php _e( 'Password', 'theme-my-login' ); ?><span class="text-red">*</span></label>
								<input type="password" name="pwd"  class="form-control input-lg" id="user_pass<?php $template->the_instance(); ?>" placeholder="Password"  value="" size="20" autocomplete="off" />
							</div>
							<?php do_action( 'login_form' ); ?>
					
							<div class="checkbox">
							    <label>
							      <input type="checkbox"   for="rememberme<?php $template->the_instance(); ?>" />  <?php esc_attr_e( 'Remember Me', 'theme-my-login' ); ?>
							    </label>
							  </div>
							  <div class="form-group">	
							  	<div class="g-recaptcha" data-sitekey="6LdsLw0TAAAAAMMYAGL8Jq4HYvdQ2O3F1IBzvS3s"></div>					
							</div>
						
							<input type="submit" class="btn btn-primary btn-lg" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" value="<?php esc_attr_e( 'Log In', 'theme-my-login' ); ?>" />
							<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'login' ); ?>" />
							<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
							<input type="hidden" name="action" value="login" />
							
						</form>
					</div>
					<div class="panel-footer ">
						<?php $template->the_action_links( array( 'login' => false ) ); ?>
					</div>
				</div>
				</div>
			</div>			
		</div>
	</div>




