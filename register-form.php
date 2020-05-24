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
									<h4>Регистрация</h4>
									<p class="text-lighten">
										<?php $template->the_action_template_message( 'register' ); ?>
									<?php $template->the_errors(); ?>
									</p>
								</div>
				<div class="panel-body">
					
					
					<form name="registerform" id="registerform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'register' ); ?>" method="post">
						<p>
							<label for="user_login<?php $template->the_instance(); ?>"><?php _e( 'Username', 'theme-my-login' ); ?></label>
							<input type="text" name="user_login" id="user_login<?php $template->the_instance(); ?>" class="form-control input-lg" value="<?php $template->the_posted_value( 'user_login' ); ?>" size="20" />
						</p>

						<p>
							<label for="user_email<?php $template->the_instance(); ?>"><?php _e( 'E-mail', 'theme-my-login' ); ?></label>
							<input type="text" name="user_email" id="user_email<?php $template->the_instance(); ?>" class="form-control input-lg" value="<?php $template->the_posted_value( 'user_email' ); ?>" size="20" />
						</p>


						<?php do_action( 'register_form' ); ?>

						<p id="reg_passmail<?php $template->the_instance(); ?>"><?php echo apply_filters( 'tml_register_passmail_template_message', __( 'A password will be e-mailed to you.', 'theme-my-login' ) ); ?></p>
						<div class="g-recaptcha" data-sitekey="6LdsLw0TAAAAAMMYAGL8Jq4HYvdQ2O3F1IBzvS3s"></div>
						<p class="submit">
							<input type="submit" name="wp-submit" class="btn btn-primary btn-lg" id="wp-submit<?php $template->the_instance(); ?>" value="<?php esc_attr_e( 'Register', 'theme-my-login' ); ?>" />
							<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'register' ); ?>" />
							<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
							<input type="hidden" name="action" value="register" />
						</p>
					</form>
					<?php $template->the_action_links( array( 'register' => false ) ); ?>
				</div>
				</div>
			</div>
		</div>			
	</div>
</div>