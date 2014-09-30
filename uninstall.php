<?php
$options = get_option('prosper_advanced');

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit ();

add_action('admin_init', array($this, 'prosperStoreRemove'));
	
if ( true == $options['Option_Delete'] || 1 == $options['Option_Delete'] )
{
    $options = array('prosperSuite', 'prosper_productSearch', 'prosper_advanced', 'prosper_themes', 'prosper_prosperent_suite', 'prosperent_store_page_id' , 'prosperent_store_page_name', 'prosperent_store_page_title', 'widget_prosperent_store', 'widget_prosper_top_products');
    foreach ($options as $option)
    {
        delete_option($option);
    }
}

