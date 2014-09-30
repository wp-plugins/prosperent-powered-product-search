<?php
/**
 * ProsperAdmin Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperShopAdminController
{
    /**
     * the class constructor
     *
     * @package 
     * @subpackage 
     *
     */
    public function __construct()
    {
		add_action('admin_menu', array($this, 'registerSettingsPage' ), 5);
		add_action( 'network_admin_menu', array( $this, 'registerNetworkSettingsPage' ) );
		
		require_once(PROSPERSHOP_MODEL . '/Admin.php');
		$prosperAdmin = new Model_Shop_Admin();
		
		add_action( 'admin_init', array( $prosperAdmin, 'optionsInit' ) );
		add_action( 'admin_enqueue_scripts', array( $prosperAdmin, 'prosperAdminCss' ));	
		add_action( 'init', array( $prosperAdmin, 'init' ), 20 );
		add_filter( 'plugin_action_links', array( $prosperAdmin, 'addActionLink' ), 10, 2 );
	}
		
	/**
	 * Register the menu item and its sub menu's.
	 *
	 * @global array $submenu used to change the label on the first item.
	 */
	public function registerSettingsPage() 
	{
		add_menu_page(__('ProsperShop Settings', 'prosperent-suite'), __( 'ProsperShop', 'prosperent-suite' ), 'manage_options', 'prospershop_general', array( $this, 'generalPage' ), PROSPERSHOP_IMG . '/prosperentWhite.png' );
		add_submenu_page('prospershop_general', __('ProsperShop', 'prosperent-suite' ), __( 'Shop Settings', 'prosperent-suite' ), 'manage_options', 'prospershop_productSearch', array( $this, 'productPage' ) );
		add_submenu_page('prospershop_general', __( 'Advanced Options', 'prosperent-suite' ), __( 'Advanced Settings', 'prosperent-suite' ), 'manage_options', 'prospershop_advanced', array( $this, 'advancedPage' ) );
		add_submenu_page('prospershop_general', __( 'Themes', 'prosperent-suite' ), __( 'Themes', 'prosperent-suite' ), 'manage_options', 'prospershop_themes', array( $this, 'themesCssPage' ) );
		
		global $submenu;
		if (isset($submenu['prospershop_general']))
			$submenu['prospershop_general'][0][0] = __('General Settings', 'prosperent-suite' );
		
	}	
		
	/**
	 * Register the settings page for the Network settings.
	 */
	function registerNetworkSettingsPage() 
	{
		add_menu_page( __('Prosperent Suite Settings', 'prosperent-suite'), __( 'Prosperent', 'prosperent-suite' ), 'delete_users', 'prospershop_general', array( $this, 'networkConfigPage' ), PROSPERSHOP_IMG . '/prosperentWhite.png' );
	}
		
	/**
	 * Loads the form for the network configuration page.
	 */
	function networkConfigPage() 
	{
		require_once(PROSPERSHOP_VIEW . '/prosperadmin/network-phtml.php' );
	}
		
	/**
	 * Loads the form for the general settings page.
	 */
	public function generalPage() 
	{
		if ( isset( $_GET['page'] ) && 'prospershop_general' == $_GET['page'] )
			require_once( PROSPERSHOP_VIEW . '/prosperadmin/general-phtml.php' );
	}
		
	/**
	 * Loads the form for the product search page.
	 */
	public function productPage() 
	{
		if ( isset( $_GET['page'] ) && 'prospershop_productSearch' == $_GET['page'] )
			require_once( PROSPERSHOP_VIEW . '/prosperadmin/search-phtml.php' );
	}	
	
	/**
	 * Loads the form for the product search page.
	 */
	public function advancedPage() 
	{	
		if ( isset( $_GET['page'] ) && 'prospershop_advanced' == $_GET['page'] )
			require_once( PROSPERSHOP_VIEW . '/prosperadmin/advanced-phtml.php' );
	}	
	
	/**
	 * Loads the form for the product search page.
	 */
	public function themesCssPage() 
	{	
		if ( isset( $_GET['page'] ) && 'prospershop_themes' == $_GET['page'] )
			require_once( PROSPERSHOP_VIEW . '/prosperadmin/themes-phtml.php' );
	}	
}
 
$prosperShopAdmin = new ProsperShopAdminController;