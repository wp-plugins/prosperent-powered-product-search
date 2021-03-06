<?php
require_once(PROSPERSHOP_MODEL . '/Base.php');
/**
 * Admin Abstract Model
 *
 * @package Model
 */
class Model_Shop_Admin extends Model_Shop_Base
{
	/**
	 * @var string $currentOption The option in use for the current admin page.
	 */
	public $currentOption = 'prosperSuite';

	/**
	 * @var array $adminPages Array of admin pages that the plugin uses.
	 */
	public $adminPages = array('prosper_general', 'prosper_productSearch', 'prosper_advanced', 'prosper_themes');
		
	public function init()
	{	
		if ( isset( $_GET['deleteRecent'] ) && wp_verify_nonce( $_GET['nonce'], 'prosper_delete_recent' ) && current_user_can( 'manage_options' ) ) 
		{
			$this->deleteRecent($_GET['deleteRecent']);
			wp_redirect( admin_url( 'admin.php?page=prosper_productSearch' ) );
		}
    }	
		
	public function deleteRecent($optNum) 
	{	
		$options = get_option('prosper_productSearch');
		$intOptNum = intval($optNum);			
				
		array_splice($options['recentSearches'], $intOptNum, 1);

		update_option('prosper_productSearch', $options);
	}
	
	public function prosperAdminCss()
	{
		wp_register_style( 'prospere_admin_style', PROSPERSHOP_URL . 'includes/css/admin.min.css', array(), $this->getVersion() );
        wp_enqueue_style( 'prospere_admin_style');
	}
		
	/**
	 * Add a link to the settings page to the plugins list
	 *
	 * @staticvar string $this_plugin holds the directory & filename for the plugin
	 * @param array  $links array of links for the plugins, adapted when the current plugin is found.
	 * @param string $file  the filename for the current plugin, which the filter loops through.
	 * @return array $links
	 */
	public function addActionLink( $links, $file ) 
	{
		static $this_plugin;

		if ( empty( $this_plugin ) ) 
			$this_plugin = 'prosper-shop/prosperShop.php';

		if ( $file == $this_plugin ) 
		{
			$settings_link = '<a href="' . admin_url( 'admin.php?page=prospershop_general' ) . '">' . __( 'Settings', 'prosperent_suite' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
		
	/**
	 * Register all the options needed for the configuration pages.
	 */
	public function optionsInit() 
	{
		register_setting( 'prosperent_options', 'prosperSuite' );
		register_setting( 'prosperent_products_options', 'prosper_productSearch' );
		register_setting( 'prosperent_advanced_options', 'prosper_advanced' );
		register_setting( 'prosperent_themescss_options', 'prosper_themes' );
		register_setting( 'prosperent_generator_options', 'prosper_generator' );
		
		if ( function_exists( 'is_multisite' ) && is_multisite() ) 
		{
			if ( get_option( 'prosperSuite' ) == '1pseo_social' )
			{
				delete_option( 'prosperSuite' );
			}
			register_setting( 'prosperent_multisite_options', 'prosper_multisite' );
		}
	}

	function multisiteDefaults() 
	{
		$option = get_option( 'prosperSuite' );
		if ( function_exists( 'is_multisite' ) && is_multisite() && !is_array( $option ) ) 
		{
			$options = get_site_option( 'prosper_multi' );
			if ( is_array( $options ) && isset( $options['defaultblog'] ) && !empty( $options['defaultblog'] ) && $options['defaultblog'] != 0 ) 
			{
				foreach ( getProsperOptionsArray() as $prosper_option ) 
				{
					update_option( $prosper_option, get_blog_option( $options['defaultblog'], $prosper_option ) );
				}
			}
			$option['ms_defaults_set'] = true;
			update_option( 'prosperSuite', $option );
		}
	}

	/**
	 * Check whether the current user is allowed to access the configuration.
	 *
	 * @return boolean
	 */
	function grantAccess() 
	{
		if ( !function_exists( 'is_multisite' ) || !is_multisite() )
			return true;

		$options = get_site_option( 'prosper_multi' );
		if ( !is_array( $options ) || !isset( $options['access'] ) )
			return true;

		if ( $options['access'] == 'superadmin' && !is_super_admin() )
			return false;

		return true;
	}
	
	/**
	 * Create a Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @param string $class   The class of the object.
	 * @return string
	 */
	public function checkbox( $var, $label, $label_left = false, $option = '', $tooltip = '', $class = 'prosper_checkbox') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;
		
		if ( $options[$var] === true )
			$options[$var] = 1;

		if ( $label_left !== false ) 
		{
			if ( !empty( $label ) )
				$label .= ':';
			$output_label = '<label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . '</label>' . $tooltip;
			$class        = $class;
		} 
		else 
		{
			$output_label = '<label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . '</label>' . $tooltip;
			$class        = $class . ' double';
		}
		

		$output_input = '<input class="' . $class . '" type="checkbox" value="1" id="' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" '  . checked( $options[$var], 1, false ) . '/>';

		if ( $label_left !== false ) 
		{
			$output = $output_label . $output_input;
		} 
		else 
		{
			$output = $output_input . $output_label;
		}

		return $output . '<br class="clear" />';
	}

	/**
	 * Create a Inline Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @return string
	 */
	public function checkboxinline( $var, $label, $label_left = false, $arrayNum, $option = '') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;
		
		$options = get_option( $option );
		
		if ( !isset( $options[$var][$arrayNum] ) )
			$options[$var][$arrayNum] = false;
			
		if ( $options[$var][$arrayNum] === true )
			$options[$var][$arrayNum] = 1;
			
		if ( $label_left !== false ) 
		{
			if ( !empty( $label_left ) )
				$label_left .= ':';
			$output_label = '<label class="prosper_checkboxinline" for="' . esc_attr( $var ) . '">' . $label_left . '</label>';
			$class        = 'prosper_checkboxinline';
		} 
		else 
		{
			$output_label = '<label class="prosper_checkboxinline" for="' . esc_attr( $var ) . '">' . $label . '</label>';
			$class        = 'prosper_checkboxinline double';
		}

		$output_input = "<input class='$class' type='checkbox' value='1' id='" . esc_attr( $var ) . "' name='" . esc_attr( $option) . "[" . esc_attr( $var ) . "][" . $arrayNum . "]' " . checked( $options[$var][$arrayNum], 1, false ) . "/>";

		if ( $label_left !== false ) {
			$output = $output_label . $output_input . '<label class="prosper_checkboxinline" for="' . esc_attr( $var ) . '">' . $label . '</label>';
		} else 
		{
			$output = $output_input . $output_label;
		}

		return $output;
	}
		
	/**
	 * Create a Text input field.
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @param string $class   The class of the object.
	 * @return string
	 */
	public function textinput( $var, $label, $option = '', $tooltip = '', $class = 'prosper_textinput') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );

		return '<label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . ':' . $tooltip . '</label><input class="' . $class . '" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']" value="' . $val . '"/>'. '<br class="clear" />';
	}

	/**
	 * Create a Text input field.
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.	 
	 * @param string $tooltip The tooltip for the option
	 * @return string
	 */
	public function textinputnewinline( $var, $arrayNum, $option = '', $tooltip = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var][$arrayNum] ) )
			$val = esc_attr( $options[$var][$arrayNum] );
			
		return '<input class="prosper_textinput" style="width:auto;margin:2px;" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . $var . '][' . $arrayNum . ']" value="' . $val . '"/>' . $tooltip;	
	}
	
	/**
	 * Create a Text input field.
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.	 
	 * @param string $tooltip The tooltip for the option
	 * @return string
	 */
	public function textinputinline( $var, $label, $arrayNum, $option = '', $tooltip = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var][$arrayNum] ) )
			$val = esc_attr( $options[$var][$arrayNum] );
			
		return '<label class="prosper_textinputinline" for="' . esc_attr( $var ) . '">' . $label . ':' . $tooltip . '</label><input class="prosper_textinputinline" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . $var . '][' . $arrayNum . ']" value="' . $val . '"/>';	
	}
	
	/**
	 * Create a hidden input field.
	 *
	 * @param string $var    The variable within the option to create the hidden input for.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function hidden( $var, $option = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );

		return '<input type="hidden" id="hidden_' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']" value="' . $val . '"/>';
	}

	/**
	 * Create a Select Box.
	 *
	 * @param string $var     The variable within the option to create the select for.
	 * @param string $label   The label to show for the variable.
	 * @param array  $values  The select options to choose from.
	 * @param string $option  The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @param string $class   The class of the object.
	 * @return string
	 */
	public function select( $var, $label, $values, $option = '', $tooltip = '', $class = 'prosper_select' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$var_esc = esc_attr( $var );
		$output  = '<label class="' . $class . '" for="' . $var_esc . '">' . $label . ':' . $tooltip . '</label>';
		$output .= '<select class="' . $class . '" name="' . $option . '[' . $var_esc . ']" id="' . $var_esc . '">';

		foreach ( $values as $value => $label ) {
			$sel = '';
			if ( isset( $options[$var] ) && $options[$var] == $value )
				$sel = 'selected="selected" ';

			if ( !empty( $label ) )
				$output .= '<option ' . $sel . 'value="' . esc_attr( $value ) . '">' . $label . '</option>';
		}
		$output .= '</select>';
		return $output . '<br class="clear"/>';
	}
	
	/**
	 * Create a Radio input field.
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param array  $values The radio options to choose from.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function radio( $var, $values, $label, $option = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;

		$var_esc = esc_attr( $var );

		$output = '<label class="prosper_radio">' . $label . ':</label><br><br><span style="margin-left:40px;">';
		if (empty($label))
		{
			$output = '<label class="prosper_radio"></label>';
		}
		
		foreach ( $values as $key => $value ) {
			$key = esc_attr( $key );
			$output .= '<input type="radio" class="prosper_radio" id="' . $var_esc . '-' . $key . '" name="' . esc_attr( $option ) . '[' . $var_esc . ']" value="' . $key . '" ' . ( $options[$var] == $key ? ' checked="checked"' : '' ) . ' /> <label class="prosper_radiofor" for="' . $var_esc . '-' . $key . '">' . esc_attr( $value ) . '</label>';
		}
		$output .= '</span>';

		return $output;
	}

	/**
	 * Create a MultiCheckbox input field.
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param array  $values The checkbox options to choose from.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option.
	 * @return string
	 */
	public function multiCheckbox( $var, $values, $label, $option = '' , $tooltip = '') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;

		$var_esc = esc_attr( $var );

		$output = '<label class="prosper_radio">' . $label . ':' . $tooltip . '</label><br><br><span style="margin-left:40px;">';
		if (empty($label))
		{
			$output = '<label class="prosper_radio"></label>';
		}

		foreach ( $values as $key => $value ) {

			$key = esc_attr( $key );
			$output .= '<input type="checkbox" class="prosper_radio" id="' . $var_esc . '-' . $key . '" name="' . esc_attr( $option ) . '[' . $var_esc . ']['.$key.']" value="' . $key . '" ' . ( $options[$var][$key] == $key ? ' checked="checked"' : '' ) . ' /> <label class="prosper_radiofor" for="' . $var_esc . '-' . $key . '">' . esc_attr( $value ) . '</label>';
		}
		$output .= '</span>';

		return $output;
	}	
	
	/**
	 * Create a postbox widget.
	 *
	 * @param string $id      ID of the postbox.
	 * @param string $title   Title of the postbox.
	 * @param string $content Content of the postbox.
	 */
	public function postbox( $id, $title, $content ) 
	{
		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="prosperbox">
			<h2><?php echo $title; ?></h2>
			<?php echo $content; ?>
		</div>
	<?php
	}


	/**
	 * Create a form table from an array of rows.
	 *
	 * @param array $rows Rows to include in the table.
	 * @return string
	 */
	public function form_table( $rows ) 
	{
		$content = '<table class="form-table">';
		foreach ( $rows as $row ) {
			$content .= '<tr><th valign="top" scrope="row">';
			if ( isset( $row['id'] ) && $row['id'] != '' )
				$content .= '<label for="' . esc_attr( $row['id'] ) . '">' . esc_html( $row['label'] ) . ':</label>';
			else
				$content .= esc_html( $row['label'] );
			if ( isset( $row['desc'] ) && $row['desc'] != '' )
				$content .= '<br/><small>' . esc_html( $row['desc'] ) . '</small>';
			$content .= '</th><td valign="top">';
			$content .= $row['content'];
			$content .= '</td></tr>';
		}
		$content .= '</table>';
		return $content;
	}
	
	/**
	 * Generates the header for admin pages
	 *
	 * @param string $title          The title to show in the main heading.
	 * @param bool   $form           Whether or not the form should be included.
	 * @param string $option         The long name of the option to use for the current page.
	 * @param string $optionshort    The short name of the option to use for the current page.
	 * @param bool   $contains_files Whether the form should allow for file uploads.
	 */
	public function adminHeader( $title, $form = true, $option = 'prosperent_options', $optionshort = 'prosperSuite', $contains_files = false ) 
	{
		?>
		<div class="wrap">
		<?php
		if ( ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' ) || ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ) ) {
			$msg = __( 'Settings updated', 'prosperent-suite' );

			echo '<div id="message" style="width:800px;" class="message updated"><p><strong>' . esc_html( $msg ) . '.</strong></p></div>';
		}
		?>
		<img src="<?php echo PROSPERSHOP_IMG . '/Gears-32.png'; ?>"/><?php echo '<h1 style="margin-left:8px;display:inline-block;font-size:34px;">ProsperShop</h1>';?>

		<h1 id="prosper-title"><?php echo $title; ?></h1>
		<div id="prosper_content_top" class="postbox-container" style="min-width:400px; width:800px; padding: 0 20px 0 0;">
		<div class="metabox-holder">
		<div class="meta-box-sortables">
		<?php
		if ( $form ) 
		{
			echo '<form action="' . admin_url( 'options.php' ) . '" method="post" id="prosper-conf"' . ( $contains_files ? ' enctype="multipart/form-data"' : '' ) . '>';
			settings_fields( $option );
			$this->currentOption = $optionshort;
		}
	}
	
	/**
	 * Generates the footer for admin pages
	 *
	 * @param bool $submit Whether or not a submit button should be shown.
	 */
	public static function adminFooter( $submit = true ) 
	{
		if ( $submit ) 
		{
			?>
			<div class="submit"><input type="submit" class="button-primary" name="submit" value="<?php _e( "Save Settings", 'prosperent-suite' ); ?>"/></div>
			<?php 
		} 
		?>
		</form>
		</div>
		</div>
		</div>
		</div>
	<?php
	}
}