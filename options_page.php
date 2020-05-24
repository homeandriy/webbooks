<?php
add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

function theme_options_init(){
register_setting( 'wpuniq_options', 'wpuniq_theme_options');
}

function theme_options_add_page() {
add_menu_page( __( 'Настройки Темы', 'WP-Unique' ), __( 'Настройки Темы', 'WP-Unique' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}
function theme_options_do_page() { global $select_options; if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;
?>

<div class="wrap">
	<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
	<div id="message" class="updated">
		<p><strong><?php _e( 'Настройки сохранены', 'WP-Unique' ); ?></strong></p>
	</div>
	<?php endif; ?>
</div>

<form method="post" action="options.php">
	<?php settings_fields( 'wpuniq_options' ); ?>
	<?php $options = get_option( 'wpuniq_theme_options' ); ?>
	<table width="600" border="0">
		<tr>
			<td>Поле1:</td>
			<td><input type="text" name="wpuniq_theme_options[field_1]" id="wpuniq_theme_options[field_1]" value="<?php echo $options[field_1];?>" /></td>
		</tr>
		<tr>
			<td>Поле2:</td>
			<td><input type="text" name="wpuniq_theme_options[field_2]" id="wpuniq_theme_options[field_2]" value="<?php echo $options[field_2];?>" /></td>
		</tr>
		<tr>
			<td>Поле3:</td>
			<td><input type="text" name="wpuniq_theme_options[field_3]" id="wpuniq_theme_options[field_3]" value="<?php echo $options[field_3];?>" /></td>
		</tr>
		<tr>
			<td>Поле4:</td>
			<td><input type="text" name="wpuniq_theme_options[field_4]" id="wpuniq_theme_options[field_4]" value="<?php echo $options[field_4];?>" /></td>
		</tr>
		<tr>
			<td>Приветствие посетителям сайта:</td>
			<td><textarea name="wpuniq_theme_options[hello_text]" id="wpuniq_theme_options[hello_text]"><?php echo $options[hello_text];?></textarea></td>
		</tr>
		<tr>
			<td>Расположение сайдбара:</td>
			<td><select name="wpuniq_theme_options[sidebar_pos]" id="wpuniq_theme_options[sidebar_pos]">
				<option value="left"<?php if($options[sidebar_pos]=='left') echo ' selected="selected"';?>>Слева</option>
				<option value="right"<?php if($options[sidebar_pos]=='right') echo ' selected="selected"';?>>Справа</option>
			</select> </td>
		</tr>
		<tr>
			<td>Показывать банер:</td>
			<td><input type="checkbox" name="wpuniq_theme_options[show_baner]" id="wpuniq_theme_options[show_baner]" value="1"<?php if($options[show_baner]=='1') echo ' checked="checked"';?> /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Применить" /></td>
		</tr>
	</table>
</form>

<?php } ?>