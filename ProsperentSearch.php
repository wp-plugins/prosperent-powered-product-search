<?php
/*
Plugin Name: Prosperent Product Search
Description: Plugin designed to add a product search to an existing vbulletin installations using Prosperent's API.
Version: 1.6
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

add_shortcode('php','php_handler');
add_shortcode('PHP','php_handler');
add_shortcode('allowphp','php_handler');
add_shortcode('ALLOWPHP','php_handler');
add_filter('the_content', 'apip_advanced_filter',0);

function apip_advanced_filter($args)
{
    $options = get_option("allowPHP_options");
    if(isset($options['use_advanced_filter'])){
        if($options['use_advanced_filter'] == "1"){
            remove_shortcode("php");
            $args = str_ireplace("[php]","<?php ",$args);
            $args = str_ireplace("[/php]"," ?>",$args);
            $args = str_ireplace("[php useadvancedfilter]","<?php ",$args);
            $args = str_ireplace("[/php useadvancedfilter]"," ?>",$args);
            ob_start();
            eval("?>".$args);
            $returned = ob_get_clean();
            return $returned;
        }
    }
    $args = str_ireplace("[php useadvancedfilter]","<?php ",$args);
    $args = str_ireplace("[/php useadvancedfilter]"," ?>",$args);
    ob_start();
    eval("?>".$args);
    $returned = ob_get_clean();
    return $returned;
}

function php_handler($args, $content="")
{
    global $is_comment;
    global $wpdb;
    $options = get_option("allowPHP_options");
    if(isset($options['preparse'])){$preparse = $options['preparse'];}else{$preparse = 0;}
    if($is_comment){return "";}
    $res = "";
    extract( shortcode_atts(array('debug' => 0,'silentdebug' => 0, 'function' => -1, 'mode'=>''), $args));
    if(!isset($args['mode'])){$mode="";}else{$mode = $args['mode'];}
    if(!isset($args['debug'])){$debug="0";}else{$debug = $args['debug'];}
    if(!isset($args['silentdebug'])){$silentdebug="0";}else{$silentdebug = $args['silentdebug'];}
    if($debug == 1){error_reporting(E_ALL);ini_set("display_errors","1");}
    if($function == ""){$function == "-1";}
    if($function == -1){
        if(($preparse!= 1 && $mode != "old") || $mode == "new"){
            #goodregextouse: /([\[])([\/]*[\d\w][\s\d\w\=\"\']*)([\]])/
            #stage1 ([\[])([\/]*[\d\w][\s\d\w="'.$;*([\/]*)([\]])*
            #stage2 ([){1}([/]*[\d\w]+[\w\d\s  ]*?[ ]*?)([/]*\]){1}
            #stage3 (\[){1}([/]{0,1}[\d\w]+[\w\d\s  =\'\"\.\$]*?[ ]*?)([/]*\]){0,1}
            #stage4 (\[{1})([\/]{0,1})([a-zA-z]{1}[a-zA-Z0-9]*[^\'\"])([a-zA-Z0-9 \!\"\£\$\%\^\&\*\*\(\)\_\-\+\=\|\\\,\.\/\?\:\;\@\'\#\~\{\[\}\]\¬\¦\`\<\>]*)([\/]{0,1})(]{1})
            $content = strip_tags($content);
            $count = "";
            $content = preg_replace("/(\[{1})([\/]*)([a-zA-z\/]{1}[a-zA-Z0-9]*[^\'\"])([a-zA-Z0-9 \!\"\£\$\%\^\&\*\*\(\)\_\-\+\=\|\\\,\.\/\?\:\;\@\'\#\~\{\}\¬\¦\`\<\>]*)([\/]*)([\]]{1})/ix","<$3$4>",$content,"-1", $count);
            $content = htmlspecialchars($content, ENT_NOQUOTES);
            $content = str_replace("&amp;#8217;","'",$content);
            $content = str_replace("&amp;#8216;","'",$content);
            $content = str_replace("&amp;#8242;","'",$content);
            $content = str_replace("&amp;#8220;","\"",$content);
            $content = str_replace("&amp;#8221;","\"",$content);
            $content = str_replace("&amp;#8243;","\"",$content);
            $content = str_replace("&amp;#039;","'",$content);
            $content = str_replace("&#039;","'",$content);
            $content = str_replace("&amp;#038;","&",$content);
            $content = str_replace("&amp;gt;",'>',$content);
            $content = str_replace("&amp;lt;",'<',$content);
            $content = htmlspecialchars_decode($content);
        }
        else{
            $content =(htmlspecialchars($content,ENT_QUOTES));$content = str_replace("&amp;#8217;","'",$content);$content = str_replace("&amp;#8216;","'",$content);$content = str_replace("&amp;#8242;","'",$content);$content = str_replace("&amp;#8220;","\"",$content);$content = str_replace("&amp;#8221;","\"",$content);$content = str_replace("&amp;#8243;","\"",$content);$content = str_replace("&amp;#039;","'",$content);$content = str_replace("&#039;","'",$content);$content = str_replace("&amp;#038;","&",$content);$content = str_replace("&amp;lt;br /&amp;gt;"," ", $content);$content = htmlspecialchars_decode($content);$content = str_replace("<br />"," ",$content);$content = str_replace("<p>"," ",$content);$content = str_replace("</p>"," ",$content);$content = str_replace("[br/]","<br/>",$content);$content = str_replace("\\[","&#91;",$content);$content = str_replace("\\]","&#93;",$content);$content = str_replace("[","<",$content);$content = str_replace("]",">",$content);$content = str_replace("&#91;",'[',$content);$content = str_replace("&#93;",']',$content);$content = str_replace("&gt;",'>',$content);$content = str_replace("&lt;",'<',$content);
        }
    }
    else{
        $show404 = $options['show404'];
        $fourohfourmsg = $options['fourohfourmsg'];
        if($fourohfourmsg != 0){
            $fourohfourmsg = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."allowPHP_functions WHERE id = '".$fourohfourmsg."';");
            $fourohfourmsg = htmlspecialchars_decode($fourohfourmsg[0]->function);
        }
        else{
            $fourohfourmsg = '?><div style="font-weight:bold; color:red">Error 404: Function Not Found</div>';
        }
        $id = $args['function'];
        $sql = "SELECT function FROM ".$wpdb->prefix."allowPHP_functions WHERE id='".$id."'";
        $res = $wpdb->get_results($wpdb->prepare($sql));
        if(sizeof($res) == 0){
            if($show404 == 1){$content = $fourohfourmsg;}
        }
        else{
            $content = htmlspecialchars_decode($res[0]->function);
        }
    }
    ob_start();
    eval ($content);
    if($debug == 1||$silentdebug == 1){
        if($silentdebug == 1){
            echo "\n\n<!-- ALLOW PHP SILENT DEBUG MODE - - > \n\n\n";
        }
        else{
            echo "<hr />";
            echo "<p align='center'>Allow PHP Debug</p>";
        }
        if(sizeof($res)==0 && $function != -1){
            $content = "Function id : $function : cannot be found<br/>";
        }else{
            $content =(htmlspecialchars($content,ENT_QUOTES));
        }
        echo ("<pre>".$content."</pre>");
        if($silentdebug == 1){
            echo "\n\n\n<- - END ALLOW PHP SILENT DEBUG MODE -->\n\n";
        }
        else{
            echo "<p align='center'>End Allow PHP Debug</p>";
            echo "<hr />";
        }
    }
    $returned = ob_get_clean();
    return $returned;
}

function prospere_stylesheets() {
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
    add_menu_page('Prosperent Plugin Settings', 'Prosperent Settings', 'administrator', __FILE__, 'prosperent_settings_page', plugins_url('/img/prosperent.png', __FILE__));

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
                        <input type="radio" name="Enable_Facets" value="1" <?php checked( '1', get_option( 'Enable_Facets' ) ); ?>> Enable<br>
                        <input type="radio" name="Enable_Facets" value="0" <?php checked( '0', get_option( 'Enable_Facets' ) ); ?>> Disable
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Api Limit</b> (Number of results, max = 1000)</th>
                    <td><input type="text" name="Api_Limit" value="<?php echo get_option('Api_Limit'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Pagination Limit</b> (Results to display on each page)</th>
                    <td><input type="text" name="Pagination_Limit" value="<?php echo get_option('Pagination_Limit'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Logo Image</b> (Display the original sized Prosperent Logo. Size is 167px x 50px.)</th>
                    <td>
                        <input type="radio" name="Logo_Image" value="1" <?php checked( '1', get_option( 'Logo_Image' ) ); ?>> Enable<br>
                        <input type="radio" name="Logo_Image" value="0" <?php checked( '0', get_option( 'Logo_Image' ) ); ?>> Disable
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Logo Image- Small</b> (Display the smaller Prosperent Logo. Size is 100px x 30px.)</th>
                    <td>
                        <input type="radio" name="Logo_imageSmall" value="1" <?php checked( '1', get_option( 'Logo_imageSmall' ) ); ?>> Enable<br>
                        <input type="radio" name="Logo_imageSmall" value="0" <?php checked( '0', get_option( 'Logo_imageSmall' ) ); ?>> Disable
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><b>Default Sort</b> (Sets the sort type default. relevance desc = Relevancy, price asc = Low to High, price desc = High to Low)</th>
                    <td><input type="text" name="Default_Sort" value="<?php echo get_option('Default_Sort'); ?>" /></td>
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
            </table>

            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>

        </form>
    </div>
    <?php
}

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'my_plugin_install');

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'my_plugin_remove' );

function my_plugin_install()
{
    global $wpdb;

    $the_page_title = 'product';
    $the_page_name = 'Prosperent Search';

    // the menu entry...
    delete_option("my_plugin_page_title");
    add_option("my_plugin_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name");
    add_option("my_plugin_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id");
    add_option("my_plugin_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "[php]
                               include(plugin_dir_path(__FILE__) . 'products.php');
                               [/php]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(2); // the default 'Uncatrgorised'

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

    delete_option( 'my_plugin_page_id' );
    add_option( 'my_plugin_page_id', $the_page_id );

}

function my_plugin_remove()
{
    global $wpdb;

    $the_page_title = get_option( "my_plugin_page_title" );
    $the_page_name = get_option( "my_plugin_page_name" );

    //  the id of our page...
    $the_page_id = get_option( 'my_plugin_page_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("my_plugin_page_title");
    delete_option("my_plugin_page_name");
    delete_option("my_plugin_page_id");

}

function my_plugin_query_parser( $q )
{
    $the_page_name = get_option( "my_plugin_page_name" );
    $the_page_id = get_option( 'my_plugin_page_id' );

    $qv = $q->query_vars;

    // have we NOT used permalinks...?
    if( !$q->did_permalink AND ( isset( $q->query_vars['page_id'] ) ) AND ( intval($q->query_vars['page_id']) == $the_page_id ) ) {
        $q->set('my_plugin_page_is_called', TRUE );
        return $q;
    }
    elseif( isset( $q->query_vars['pagename'] ) AND ( ($q->query_vars['pagename'] == $the_page_name) OR ($_pos_found = strpos($q->query_vars['pagename'],$the_page_name.'/') === 0) ) ) {
        $q->set('my_plugin_page_is_called', TRUE );
        return $q;
    }
    else {
        $q->set('my_plugin_page_is_called', FALSE );
        return $q;
    }
}

add_filter( 'parse_query', 'my_plugin_query_parser' );


function Prospere_Search()
{
    ?>
    <form id="search" method="GET" action="<?php echo get_option('Parent_Directory') . '/product'; ?>">
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
                    <td class="image"><a href="http://prosperent.com" title="Prosperent"> <img src="<?php echo plugins_url('/img//logo_smaller.png', __FILE__); ?>"/> </a></td>
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
                            <td class="srchBoxCont" nowrap><input class="srch_box" type="text" maxlength="2048" name="q" size="41" title="Search Products"></td>

                            <!-- script that produces the faded 'Search Products...' in the input box and once the box is clicked on it disappears -->
                            <script type="text/javascript">
                                var form = document.getElementById('search');
                                setDefaultText(form.elements.q, 'Search Products...');

                                function setDefaultText(field, text)
                                {
                                    text = text || field.defaultText;
                                    if (field.value === '')
                                    {
                                        field.value = text;
                                        field.defaultText = text;
                                        addClass(field, 'faded');
                                    }

                                    field.onfocus = function ()
                                    {
                                        removeDefaultText(this);
                                    };

                                    field.onblur = function ()
                                    {
                                        setDefaultText(this);
                                    };
                                }

                                function removeDefaultText(field)
                                {
                                    if (field.value === field.defaultText)
                                    {
                                        field.value = '';
                                        removeClass(field, 'faded');
                                    }
                                }

                                function hasDefaultText(field)
                                {
                                    return (field.value === field.defaultText);
                                }

                                function hasClass(ele,cls)
                                {
                                    return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
                                }

                                function addClass(ele,cls)
                                {
                                    if (!this.hasClass(ele,cls)) ele.className += " "+cls;
                                }

                                function removeClass(ele,cls)
                                {
                                    if (hasClass(ele,cls))
                                    {
                                        var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
                                        ele.className=ele.className.replace(reg,' ');
                                    }
                                }
                            </script>
                            <td nowrap style="vertical-align:middle;">
                                <div class="srchButtonBorder">
                                    <input class="srch_button" type="submit" value="Search" cursor:pointer id="srchButton">
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
    <?php
}

function prospere_header() {
    do_action('prospere_header');
}

add_action('prospere_header', 'Prospere_Search');


