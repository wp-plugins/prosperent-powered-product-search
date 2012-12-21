<?php
/*
Plugin Name: Prosperent Product Search
Description: Plugin designed to add a product search to a WordPress blog using Prosperent's API.
Version: 3.0
Author: Prosperent Brandon
License: GPL2
*/

/*  Copyright 2012  Prosperent Brandon  (email : brandon@prosperent.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'wp_enqueue_scripts', 'prospere_stylesheets');
add_action('admin_menu', 'prosperent_create_menu');
add_shortcode('prosper_store','prosper_store');
add_shortcode('prosper_search', 'Prospere_Search_Short');
add_action( 'widgets_init', create_function( '', 'register_widget( "Prosper_Store_Widget" );' ) );
add_action('wp_title', 'prosper_title', 10, 3);

class Prosper_Store_Widget extends WP_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'prosperent_store_widget', 'description' => __( "Displays the Prosperent search bar") );
        parent::__construct('prosperent_store', __('Prosperent Store'), $widget_ops);
    }

    function widget( $args, $instance )
    {
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;
        Prospere_Search_Widget();
        echo $after_widget;
    }

    function update( $new_instance, $old_instance )
    {
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = $instance['title'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <?php
    }
}

function prosper_store()
{
    ob_start();
    include(plugin_dir_path(__FILE__) . 'products.php');
    $store = ob_get_clean();
    return $store;
}


function prospere_stylesheets()
{
    global $wp_styles;

    // Product Search CSS for results and search
    wp_register_style( 'prospere_main_style', plugins_url('/css/productSearch.css', __FILE__) );
    wp_enqueue_style( 'prospere_main_style' );

    // Product Search CSS for IE7, a few changes to align objects
    wp_enqueue_style('prospere_IE_7', plugins_url('/css/productSearch-IE7.css', __FILE__));
    $wp_styles->add_data('prospere_IE_7', 'conditional', 'IE 7');
}

function prosperent_create_menu()
{
    //create new top-level menu
    add_options_page('Prosperent Plugin Settings', 'Prosperent Settings', 'administrator', __FILE__, 'prosperent_settings_page', plugins_url('/img/prosperent.png', __FILE__));

    //call register settings function
    add_action( 'admin_init', 'register_prosperentSettings' );
}


function register_prosperentSettings()
{
    //register our settings
    register_setting('prosperent-settings-group', 'Api_Key');
    register_setting('prosperent-settings-group', 'Enable_Facets');
    register_setting('prosperent-settings-group', 'Api_Limit', 'intval');
    register_setting('prosperent-settings-group', 'Pagination_Limit', 'intval');
    register_setting('prosperent-settings-group', 'Logo_Image');
    register_setting('prosperent-settings-group', 'Logo_imageSmall');
    register_setting('prosperent-settings-group', 'Default_Sort');
    register_setting('prosperent-settings-group', 'Parent_Directory');
    register_setting('prosperent-settings-group', 'Merchant_Facets', 'intval');
    register_setting('prosperent-settings-group', 'Brand_Facets', 'intval');
    register_setting('prosperent-settings-group', 'Negative_Brand');
    register_setting('prosperent-settings-group', 'Negative_Merchant');
    register_setting('prosperent-settings-group', 'Starting_Query');
    register_setting('prosperent-settings-group', 'Additional_CSS');
    register_setting('prosperent-settings-group', 'Base_URL');
}

function prosper_title($post_title, $sep, $seplocation)
{
    if (is_singular() && !$_GET['q'] && get_option('Starting_Query'))
    {
        $post_title = ucwords(get_option('Starting_Query'))  . ' | ';
    }
    else if (is_singular() && $_GET['q'])
    {
        $post_title = ucwords($_GET['q'])  . ' | ';
    }
    else
    {
        $post_title = $post_title;
    }

    return $post_title;
}

function prosperent_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Prosperent Product Search</h2>

        <form method="post" action="options.php">
            <?php settings_fields('prosperent-settings-group'); ?>
            <?php do_settings_sections('prosperent-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><b>API Key</b> (Enter your Api key here so you can earn your commissions)</th>
                    <td><input type="text" name="Api_Key" value="<?php echo get_option('Api_Key'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Enable Facets</b></th>
                    <td>
                        <input type="radio" name="Enable_Facets" value="1" <?php checked('1', get_option('Enable_Facets')); ?>> Enable<br>
                        <input type="radio" name="Enable_Facets" value="0" <?php checked('0', get_option('Enable_Facets')); ?>> Disable
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Api Limit</b> (Number of results, max = 100)</th>
                    <td><input type="text" name="Api_Limit" value="<?php echo get_option('Api_Limit'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Pagination Limit</b> (Results to display on each page)</th>
                    <td><input type="text" name="Pagination_Limit" value="<?php echo get_option('Pagination_Limit'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Default Sort</b> (Sets the sort type default. relevance desc = Relevancy, price asc = Low to High, price desc = High to Low)</th>
                    <td>
                        <select name="Default_Sort">
                            <option name="Default_Sort" value="relevance desc" <?php echo selected('relevance desc', get_option('Default_Sort')); ?>>Relevancy</option>
                            <option name="Default_Sort" value="price asc" <?php echo selected('price asc', get_option('Default_Sort')); ?>>Price: High to Low</option>
                            <option name="Default_Sort" value="price desc" <?php echo selected('price desc', get_option('Default_Sort')); ?>>Price: Low to High</option>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Parent Directory</b> (If you want your product page to have a parent directory, name that here.)</th>
                    <td><input type="text" name="Parent_Directory" value="<?php echo get_option('Parent_Directory'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Merchant Facets</b> (Number of merchants to display in primary facet list)</th>
                    <td><input type="text" name="Merchant_Facets" value="<?php echo get_option('Merchant_Facets'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Brand Facets</b> (Number of merchants to display in primary facet list)</th>
                    <td><input type="text" name="Brand_Facets" value="<?php echo get_option('Brand_Facets'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Negative Brand Filters</b> (Brands to discard from results)</th>
                    <td><input type="text" name="Negative_Brand" value="<?php echo get_option('Negative_Brand'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Negative Merchant Filters</b> (Merchants to discard from results)</th>
                    <td><input type="text" name="Negative_Merchant" value="<?php echo get_option('Negative_Merchant'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Starting Query</b> (When first visited, the site will use this query if one has not been given by the user. If no starting query is set, it shows the no results page.)</th>
                    <td><input type="text" name="Starting_Query" value="<?php echo get_option('Starting_Query'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Base URL</b> (If you have a different site from '<b>your-blog.com/product</b>' that you want the search query to go to.)</th>
                    <td><input type="text" name="Base_URL" value="<?php echo get_option('Base_URL'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Additional CSS</b> (Additional CSS for the shortcode search bar.)</th>
                    <td><input type="text" name="Additional_CSS" value="<?php echo get_option('Additional_CSS'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Logo Image</b> (<b>Only for search bar in header.</b> Display the original sized Prosperent Logo. Size is 167px x 50px.)</th>
                    <td>
                        <input type="radio" name="Logo_Image" value="1" <?php checked('1', get_option('Logo_Image')); ?>> Enable<br>
                        <input type="radio" name="Logo_Image" value="0" <?php checked('0', get_option('Logo_Image')); ?>> Disable
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Logo Image- Small</b> (<b>Only for search bar in header.</b> Display the smaller Prosperent Logo. Size is 100px x 30px.)</th>
                    <td>
                        <input type="radio" name="Logo_imageSmall" value="1" <?php checked('1', get_option('Logo_imageSmall')); ?>> Enable<br>
                        <input type="radio" name="Logo_imageSmall" value="0" <?php checked('0', get_option('Logo_imageSmall')); ?>> Disable
                    </td>
                </tr>
            </table>

            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>

        </form>
    </div>
    <?php
}

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'prosperent_store_install');

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'prosperent_store_remove' );

function prosperent_store_install()
{
    global $wpdb;

    $the_page_title = 'Products';
    $the_page_name = 'Prosperent Search';

    // the menu entry...
    delete_option("prosperent_store_page_title");
    add_option("prosperent_store_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("prosperent_store_page_name");
    add_option("prosperent_store_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("prosperent_store_page_id");
    add_option("prosperent_store_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[prosper_store] [/prosper_store]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(0); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...
        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'prosperent_store_page_id' );
    add_option( 'prosperent_store_page_id', $the_page_id );

}

function prosperent_store_remove()
{
    global $wpdb;

    $the_page_title = get_option( "prosperent_store_page_title" );
    $the_page_name = get_option( "prosperent_store_page_name" );

    // the id of our page...
    $the_page_id = get_option( 'prosperent_store_page_id' );
    if( $the_page_id )
    {
        wp_delete_post( $the_page_id ); // this will trash, not delete
    }

    delete_option("prosperent_store_page_title");
    delete_option("prosperent_store_page_name");
    delete_option("prosperent_store_page_id");
}

function Prospere_Search()
{
    ?>
    <form id="search" method="GET" action="<?php echo !get_option('Base_URL') ? (!get_option('Parent_Directory') ? '/products' : '/' . get_option('Parent_Directory') . '/products') : '/' . get_option('Base_URL'); ?>">
        <table>
            <tr>
                <?php
                // if $logo_image is set to TRUE, this statement will output the Prosperent logo before the input box
                if (get_option('Logo_Image'))
                {
                    ?>
                    <td class="image"><a href="http://prosperent.com" title="Prosperent Search"> <img src="<?php echo plugins_url('/img/logo_small.png', __FILE__); ?>" /> </a></td>
                    <style type=text/css>
                        #search-input {
                            margin-bottom:5px;
                        }
                    </style>
                    <?php
                }
                if (get_option('Logo_imageSmall'))
                {
                    ?>
                    <td class="image"><a href="http://prosperent.com" title="Prosperent"> <img src="<?php echo plugins_url('/img/logo_smaller.png', __FILE__); ?>"/> </a></td>
                    <style type=text/css>
                        #branding img {
                            margin-bottom:6px;
                        }
                    </style>
                    <?php
                }

                ?>
                <td>
                    <table id="search-input" cellspacing="0">
                        <tr>
                            <td class="srchBoxCont" nowrap>
                                <input class="srch_box" type="text" name="q" placeholder="Search Products...">
                            </td>
                            <td nowrap style="vertical-align:middle;">
                                <input class="submit" type="submit" id="searchsubmit" value="Search">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <?php
}

function Prospere_Search_Short()
{
    ?>
    <div style="width:200px;<?php echo get_option('Additional_CSS'); ?>">
        <form id="searchform" method="GET" action="<?php echo !get_option('Base_URL') ? (!get_option('Parent_Directory') ? '/products' : '/' . get_option('Parent_Directory') . '/products') : '/' . get_option('Base_URL'); ?>">
            <input class="field" type="text" name="q" id="s" placeholder="Search Products">
            <input class="submit" type="submit" id="searchsubmit" value="Search">
        </form>
    </div>
    <?php
}

function Prospere_Search_Widget()
{
    ?>
    <form id="searchform" method="GET" action="<?php echo !get_option('Base_URL') ? (!get_option('Parent_Directory') ? '/products' : '/' . get_option('Parent_Directory') . '/products') : '/' . get_option('Base_URL'); ?>">
        <input class="field" type="text" name="q" id="s" placeholder="Search Products" style="width:68%;">
        <input class="submit" type="submit" id="searchsubmit" value="Search">
    </form>
    <?php
}

function prospere_header() {
    do_action('prospere_header');
}

add_action('prospere_header', 'Prospere_Search');
