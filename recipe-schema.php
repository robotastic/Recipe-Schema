<?php
/*
Plugin Name: Recipe Schema
Plugin URI: http://www.recipeschema.org
Description: Encode your recipes with microdata making it easier for Google and other search engines to index them. Doing this will add those nice pictures next to your post in the listings.
Version: 1.2.2
Author: Luke Berndt
Author URI: http://www.lukeberndt.com
License: A "Slug" license name e.g. GPL2
*/


if ( !defined( 'RECIPE_SCHEMA_VERSION_NUM' ) )
  define( 'RECIPE_SCHEMA_VERSION_NUM', '1.0' );
define( "RECIPE_SCHEMA_POSTTYPE",         "recipe-schema" );
define( 'RECIPE_SCHEMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( "RECIPE_SCHEMA_PLUGINDIR",        WP_PLUGIN_DIR . "/recipe-schema" );
define( 'RECIPE_SCHEMA_BASENAME', plugin_basename( __FILE__ ) );
define( "RECIPE_SCHEMA_LABEL_SINGLE",      "recipe" );
define( "RECIPE_SCHEMA_LABEL_PLURAL",      "recipes" );
define( "RECIPE_SCHEMA_NAME_SINGLE",      "Recipe" );
define( "RECIPE_SCHEMA_NAME_PLURAL",      "Recipes" );


/**
 * * Global PostType & Plugin Variables
 */

require_once 'inc/recipe.php';
require_once 'inc/amazon.php';
require_once 'inc/recipe-schema-client.php';
require_once 'inc/feature-image-tab.php';
require_once 'inc/AmazonECS.class.php';

if ( !class_exists( 'WP_List_Table' ) ) {
  require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}



// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
  echo "Hey!  This is just a plugin, not much it can do when called directly.";
  exit;
}


class Recipe_Schema {
  public $client;
  private $amazon;
  private $ecs;
  public $feature_image_tab;
  public $db_version;
  public function __construct() {

    $amazon_key = get_option( 'amazon_key' );
    $amazon_secret = get_option( 'amazon_secret' );
    $amazon_associate = get_option( 'amazon_associate' );
    if ( $amazon_secret && $amazon_associate && $amazon_key ) {
      $this->ecs = new AmazonECS( $amazon_key, $amazon_secret, 'com', $amazon_associate );
      $this->amazon = new Amazon( $this->ecs );
      $this->client = new Recipe_Schema_Client( $this->amazon );
    } else {
      $this->client = new Recipe_Schema_Client();
    }


    $this->feature_image_tab = new Recipe_Feature_Image_Tab();
    $this->db_version = "1.1";

    // Register hooks
    if ( is_admin() ) {
      add_action( 'admin_print_scripts', array( $this, 'add_script' ) );
      add_action( 'admin_head', array( $this->client, 'add_script_config' ) );
      add_action( 'wp_ajax_amazon_search', array( $this, 'ajax_amazon_search' ) );
      add_action( 'wp_ajax_recipe_schema', array( $this, 'ajax_recipe_schema' ) );
      add_action( 'wp_ajax_nopriv_recipe_schema',  array( $this, 'ajax_recipe_schema' ) );
      add_action( 'wp_ajax_nopriv_amazon_search',  array( $this, 'ajax_amazon_search' ) );
      add_action( 'admin_menu', array( $this, 'menu_pages' ) );
      add_action( 'add_meta_boxes', array( $this, 'add_custom_box' ) );
      add_action( 'save_post', array( $this, 'save_postdata' ), 1 , 2 );
      add_filter( 'media_upload_tabs', array( $this->feature_image_tab, 'media_upload_tabs_filter' ), 99 );
      add_action( 'media_upload_feature_image_tab', array( $this->feature_image_tab, 'media_upload_feature_tab' ) );
      add_filter( 'get_media_item_args', array( $this->feature_image_tab, 'get_media_item_args' ) );
      add_action( 'admin_init', array( $this, 'register_settings' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_css_js_pointer' ) );
      add_filter( 'plugin_action_links_' . RECIPE_SCHEMA_BASENAME, array( $this, 'settings_link' ) );
    }
    else {
      add_action( 'wp_print_styles', array( $this, 'load_thickbox' ) );
      add_action( 'wp_print_styles', array( $this, 'add_template_style' ) );
    }

    add_action( 'init', array( $this, 'create_recipe_post_type' ) );
    add_action( 'init', array( $this, 'create_recipe_taxonomies' ) );
    //add_action( 'init', array( $this, 'add_endpoints'));

    add_action( 'before_delete_post', array( $this, 'post_delete' ), 1 );

    add_action( 'the_posts', array ( $this, 'get_posts' ), -32767 );
    add_action( 'plugins_loaded', array( $this, 'update_db_check' ) );

    add_filter( 'post_class', array( $this, 'remove_hentry' ) );
    register_activation_hook( __FILE__, array( $this, 'install' ) );


    add_action( 'template_redirect', array( $this->client, 'print_template_redirect' ) );
    add_action( 'template_redirect', array( $this->client, 'email_template_redirect' ) );
    add_action( 'template_redirect', array( $this->client, 'json_template_redirect' ) );
    add_shortcode( 'recipes', array( $this, 'recipes_shortcode_tag' ) );

    //for the opengraph meta information
    //add_action('wp_head', array($this->client, 'add_open_graph_tags'),99);

  }

  /**
   * Registers the settings for the plugin.
   *
   * @return [type] [description]
   */
  function register_settings() {
    //register our settings
    register_setting( 'recipe-schema-settings-group', 'show_recipe_photo' );
    register_setting( 'recipe-schema-settings-group', 'show_print_button' );
    register_setting( 'recipe-schema-settings-group', 'show_facebook_button' );
    register_setting( 'recipe-schema-settings-group', 'show_whisk_button' );
    register_setting( 'recipe-schema-settings-group', 'amazon_key' );
    register_setting( 'recipe-schema-settings-group', 'amazon_secret' );
    register_setting( 'recipe-schema-settings-group', 'amazon_associate' );
    register_setting( 'recipe-schema-settings-group', 'amazon_cached' );
  }

  function settings_link( $links ) {
    $url = admin_url( 'options-general.php?page=' . RECIPE_SCHEMA_POSTTYPE );
    $settings_link = '<a href="' . $url . '">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
  }

  /**
   * Removes hentry from the array of classes that get printed out in the Post Classes
   *
   * @param Array   $classes The list of classes
   * @return Array          The updated list
   */
  function remove_hentry( $classes ) {

    $classes = array_diff( $classes, array( 'hentry' ) );

    return $classes;
  }


  function add_endpoints() {
    add_rewrite_endpoint( 'json', EP_RECIPE );
    add_rewrite_endpoint( 'print', EP_RECIPE );
    add_rewrite_endpoint( 'email', EP_RECIPE );

  }


  /**
   * If there is the shortcode [recipes] this function will print out the recipe.
   *
   * @param Array   $atts Attributes associated with the Shortcode
   * @return string       The text for the recipes associated with the Post
   */
  function recipes_shortcode_tag( $atts ) {
    global $wpdb, $post;
    $recipe_content = '<script type="text/javascript">function print_recipe(url) { window.open(url); return false;}</script>';

    if ( $post->post_type == 'post' ) {
      $recipe_list = get_post_meta( $post->ID, 'recipe_ids', TRUE );

      if ( $recipe_list ) {
        $querystr = "
              SELECT $wpdb->posts.post_title, $wpdb->posts.ID
              FROM $wpdb->posts
              WHERE  $wpdb->posts.post_type = '" . RECIPE_SCHEMA_POSTTYPE . "'
              AND  $wpdb->posts.ID IN (" . implode( ",", $recipe_list ) . ")";

        $recipes_array = $wpdb->get_results( $querystr, OBJECT );

        foreach ( $recipes_array  as $row ) {
          $recipe_content = $recipe_content .  $this->client->print_recipe( $row,  $post->ID );
        }
      }
    }
    return $recipe_content;
  }


  /**
   * Adds the first-install pointer/callout pointer CSS/JS & registers the action
   */
  function add_admin_css_js_pointer() {
    wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );

    add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
  }



  /**
   * Add pointer popup message when plugin first installed. It checks to see the settings to see if this is the first run. If so it adds the functionality using javascript.
   *
   * @return [type] [description]
   */
  function admin_print_footer_scripts() {
    //Check option to hide pointer after initial display
    if ( !get_option( 'recipe_schema_hide_pointer' ) ) {
      $pointer_content = "<h3>Lets get Cooking!</h3>";
      $pointer_content .= '<p>Adding Recipes to your Posts has just gotten easier. Import recipes from existing recipes or add new ones and then attach them to a Post.</p>';

      $url = admin_url( 'options-general.php?page=' . RECIPE_SCHEMA_POSTTYPE );

?>

          <script type="text/javascript">
              //<![CDATA[
              jQuery(document).ready( function($) {
                  $("#menu-plugins").pointer({
                      content: '<?php echo $pointer_content; ?>',
                      buttons: function( event, t ) {
                          button = $('<a id="pointer-close" class="button-secondary">Close</a>');
                          button.bind("click.pointer", function() {
                              t.element.pointer("close");
                          });
                          return button;
                      },
                      position: "left",
                      close: function() { }

                  }).pointer("open");

                  $("#pointer-close").after('<a id="pointer-primary" class="button-primary" style="margin-right: 5px;" href="<?php echo $url; ?>">' +
                      'Recipe Schema Settings');
              });
              //]]>
          </script>

          <?php

      //Update option so this pointer is never seen again
      update_option( 'recipe_schema_hide_pointer', 1 );
    }
  }


  public function update_db_check() {
    global $wpdb;

    if ( get_option( 'recipe_schema_db_version' ) != $this->db_version ) {
      $this->install();
    }
    if ( !isset( $wpdb->robo_ingredients ) ) {
      $wpdb->recipe_schema_ingredients = $wpdb->prefix . "recipe_schema_ingredients";
    }
  }

  function amazon_cache_install() {
    global $wpdb;

    if ( get_option( 'amazon_cached' ) ) return False;
    $cache_table = $wpdb->prefix . 'amazon_link_cache';
    $sql = "CREATE TABLE $cache_table (
                 asin varchar(10) NOT NULL,
                 cc varchar(5) NOT NULL,
                 title varchar(100) NOT NULL,
                 updated datetime NOT NULL,
                 image varchar(100),
                 price varchar(20),
                 url   varchar(300),
                 PRIMARY KEY  (asin, cc)
                 );";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    update_option( 'amazon_cached', 1 );
    return True;
  }

  function install() {
    global $wpdb;

    //Deactivate plugin if WP version too low
    if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
      deactivate_plugins( basename( __FILE__ ) );
    }

    $installed_ver = get_option( "recipe_schema_db_version" );
    $this->create_recipe_taxonomies();
    $this->register_default_taxonomies();
    $this->create_recipe_post_type();

    flush_rewrite_rules();

    $this->amazon_cache_install();


    if ( $installed_ver != $this->db_version ) {

      $table_name = $wpdb->prefix . "recipe_schema_ingredients";

      $sql = "CREATE TABLE $table_name (
       `id` int(11) unsigned NOT NULL auto_increment,
       `quantity` varchar(255) default NULL,
       `unit` varchar(255) default NULL,
       `fooditem` varchar(255) default NULL,
       `preperation` varchar(255) default NULL,
       `recipe_id` int(11) unsigned default NULL,
       `note` varchar(255) default NULL,
       `line` text,
       `header` tinyint(1) DEFAULT NULL,
       `step` int(11) DEFAULT NULL,
       PRIMARY KEY  (`id`)
     )";

      require_once ABSPATH . 'wp-admin/includes/upgrade.php';
      dbDelta( $sql );

      add_option( "recipe_schema_db_version", $this->db_version );

    }
  }

  function register_default_taxonomies() {
    // Default Cuisines
    wp_insert_term( 'American', 'cuisine' );
    wp_insert_term( 'Chinese', 'cuisine' );
    wp_insert_term( 'Indian', 'cuisine' );
    wp_insert_term( 'Italian', 'cuisine' );
    wp_insert_term( 'French', 'cuisine' );
    wp_insert_term( 'Japanese', 'cuisine' );
    wp_insert_term( 'Mediterrarean', 'cuisine' );
    wp_insert_term( 'Mexican', 'cuisine' );
    wp_insert_term( 'Seafood', 'cuisine' );
    // Default recipe_types
    wp_insert_term( 'Appetizer', 'recipe_type',
      array(
        'slug' => sanitize_title( 'Appetizer' ) . '-' . RECIPE_SCHEMA_POSTTYPE
      ) );
    wp_insert_term( 'Breakfast', 'recipe_type',
      array(
        'slug' => sanitize_title( 'Breakfast' ) . '-' . RECIPE_SCHEMA_POSTTYPE
      ) );
    wp_insert_term( 'Entrée', 'recipe_type',
      array(
        'slug' => sanitize_title( 'Entrée' ) . '-' . RECIPE_SCHEMA_POSTTYPE
      ) );
    wp_insert_term( 'Dessert', 'recipe_type' );
    wp_insert_term( 'Drink', 'recipe_type' );
    wp_insert_term( 'Salad', 'recipe_type' );
    wp_insert_term( 'Sandwich', 'recipe_type' );
    wp_insert_term( 'Sauce', 'recipe_type' );
    wp_insert_term( 'Bread', 'recipe_type' );
    wp_insert_term( 'Pasta', 'recipe_type',
      array(
        'slug' => sanitize_title( 'Pasta' ) . '-' . RECIPE_SCHEMA_POSTTYPE
      ) );
    wp_insert_term( 'Side Dish', 'recipe_type' );
    wp_insert_term( 'Snack', 'recipe_type' );
    wp_insert_term( 'Soup', 'recipe_type' );
  }

  function create_recipe_taxonomies() {
    $labelsRecipeType = array(
      'name' => _x( 'Recipe Type', 'taxonomy general name' ),
      'singular_name' => _x( 'Type', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Recipe Types' ),
      'popular_items' => __( 'Popular Recipe Types' ),
      'all_items' => __( 'All Recipe Types' ),
      'parent_item' => null,
      'parent_item_colon' => null,
      'edit_item' => __( 'Edit Recipe Type' ),
      'update_item' => __( 'Update Recipe Type' ),
      'add_new_item' => __( 'Add New Recipe Type' ),
      'new_item_name' => __( 'New Recipe Type' ),
      'separate_items_with_commas' => __( 'Separate with commas' ),
      'add_or_remove_items' => __( 'Add or remove Recipe Types' ),
      'choose_from_most_used' => __( 'Choose from the most used Recipe Type' )
    );
    register_taxonomy(
      'recipe_type',
      RECIPE_SCHEMA_POSTTYPE,
      array(
        'hierarchical' => true,
        'labels' => $labelsRecipeType,
        'label' => 'Recipe Type',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'recipe_type' ),
      )
    );

    $labelsRecipeCuisine = array(
      'name' => _x( 'Cuisine', 'taxonomy general name' ),
      'singular_name' => _x( 'Cuisine', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Cuisine' ),
      'popular_items' => __( 'Popular Cuisine' ),
      'all_items' => __( 'All Cuisine' ),
      'parent_item' => null,
      'parent_item_colon' => null,
      'edit_item' => __( 'Edit Cuisine' ),
      'update_item' => __( 'Update Cuisine' ),
      'add_new_item' => __( 'Add New Cuisine' ),
      'new_item_name' => __( 'New Cuisine' ),
      'separate_items_with_commas' => __( 'Separate with commas' ),
      'add_or_remove_items' => __( 'Add or remove Cuisine' ),
      'choose_from_most_used' => __( 'Choose from the most used Cuisine' )
    );
    register_taxonomy(
      'cuisine',
      RECIPE_SCHEMA_POSTTYPE,
      array(
        'hierarchical' => true,
        'labels' => $labelsRecipeCuisine,
        'label' => 'Cuisine',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'cuisine' ),
      )
    );


  }



  function add_template_style() {
    wp_register_style( 'default.css', RECIPE_SCHEMA_PLUGIN_URL . 'templates/default.css' );
    wp_enqueue_style( 'default.css' );
  }

  /**
   * Add script to admin page
   */
  function add_script() {
    // Build in tag auto complete script
    //wp_enqueue_script( 'suggest' );

    wp_enqueue_script( 'jquery-ui-autocomplete', 'editor' );
    wp_register_script( 'recipe-schema.js', RECIPE_SCHEMA_PLUGIN_URL . 'js/recipe-schema-1.2.js' );
    wp_enqueue_script( 'recipe-schema.js' );
    wp_register_script( 'wysiwyg', RECIPE_SCHEMA_PLUGIN_URL . 'js/wysiwyg.js' );
    wp_enqueue_script( 'wysiwyg' );

    wp_register_style( 'my-jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
    wp_enqueue_style( 'my-jquery-ui' );
    wp_register_style( 'recipe-schema.css', RECIPE_SCHEMA_PLUGIN_URL . 'css/recipe-schema.css' );
    wp_enqueue_style( 'recipe-schema.css' );
    wp_register_style( 'wysiwyg', RECIPE_SCHEMA_PLUGIN_URL . 'css/wysiwyg.css' );
    wp_enqueue_style( 'wysiwyg' );
  }

  function load_thickbox() {
    //wp_enqueue_style('thickbox');
    //wp_enqueue_script('thickbox');
  }

  public function ajax_amazon_search() {
    $suggest =  trim( esc_attr( strip_tags( $_REQUEST['term'] ) ) );
    $category =  trim( esc_attr( strip_tags( $_REQUEST['category'] ) ) );
    $page = trim( esc_attr( strip_tags( $_REQUEST['page'] ) ) );
    //error_log($suggest . '  ' . $category);

    try {
      $response  = $this->ecs->category( $category )->responseGroup( 'Images,ItemAttributes' )->optionalParameters( array( 'ItemPage' => $page ) )->search( $suggest );
      header( 'Content-Type: application/json' );


      $results=array();
      $items=array();
      if ( $response->Items->TotalResults > 1 ) {
        foreach ( $response->Items->Item as $item ) {
          
          $result=array();
          if ( isset( $item->MediumImage->URL ) )
            $result['image'] = $item->MediumImage->URL;
          if ( isset( $item->ItemAttributes->ListPrice->FormattedPrice ) )
            $result['price'] = $item->ItemAttributes->ListPrice->FormattedPrice;
          $result['title'] = $item->ItemAttributes->Title;
          $result['link'] = $item->DetailPageURL;
          $result['asin'] = $item->ASIN;
          $items[] = $result;
        }
      } else {
        $item = $response->Items->Item;
        $result=array();

        if ( isset( $item->MediumImage->URL ) )
          $result['image'] = $item->MediumImage->URL;
        if ( isset( $item->ItemAttributes->ListPrice->FormattedPrice ) )
          $result['price'] = $item->ItemAttributes->ListPrice->FormattedPrice;
        else 
          $result['price'] = '';
        $result['title'] = $item->ItemAttributes->Title;
        $result['link'] = $item->DetailPageURL;
        $result['asin'] = $item->ASIN;
        $items[] = $result;
      }
      $results['success'] = true;
      $results['items'] = $items;
      $response = json_encode( $results );
      echo $response;
      die();
    }
    catch( SoapFault $ex ) {
      $results=array();
      $results['success'] = false;
      $results['message'] = $ex->getMessage();
      $response = json_encode( $results );

      header( 'Content-Type: application/json' );
      echo $response;
      die();

    }
  }

  public function ajax_recipe_schema() {
    global $wpdb;


    //$suggest = '%' . $_REQUEST['term'] . '%';
    $suggest = '%' . trim( esc_attr( strip_tags( $_REQUEST['term'] ) ) ) . '%';

    $querystr = "
        SELECT *
        FROM $wpdb->posts
        WHERE  $wpdb->posts.post_type = '" . RECIPE_SCHEMA_POSTTYPE . "'
        AND  $wpdb->posts.post_title LIKE '" . $suggest . "'
        AND $wpdb->posts.post_status = 'publish'";

    $recipes_array = $wpdb->get_results( $querystr, OBJECT );

    $suggestions=array();
    foreach ( $recipes_array  as $row ) {
      $suggestion = array();
      $suggestion['label'] = esc_html( $row->post_title );
      $suggestion['id'] = $row->ID;
      $post_thumbnail_id= get_post_thumbnail_id( $row->ID );
      $suggestion['thumbnail_id'] = $post_thumbnail_id;
      $suggestion['thumb'] = wp_get_attachment_image_src( $post_thumbnail_id );

      $suggestions[]= $suggestion;
    }

    $response = $_GET["callback"] . "(" . json_encode( $suggestions ) . ")";
    echo $response;
    die();
  }

  public function create_recipe_post_type() {

    define( 'EP_RECIPE', 8388608 );
    register_post_type( RECIPE_SCHEMA_POSTTYPE,
      array(
        'labels' => array(
          'name' => RECIPE_SCHEMA_NAME_PLURAL,
          'singular_name' => RECIPE_SCHEMA_LABEL_SINGLE,
          'all_items' => 'All ' . RECIPE_SCHEMA_NAME_PLURAL,
          'add_new_item' => 'Add new ' . RECIPE_SCHEMA_LABEL_SINGLE,
          'edit_item' => 'Edit ' . RECIPE_SCHEMA_LABEL_SINGLE,
          'new_item' => 'New ' . RECIPE_SCHEMA_LABEL_SINGLE,
          'search_items' => 'Search ' . RECIPE_SCHEMA_NAME_PLURAL,
          'not_found' => 'No ' . RECIPE_SCHEMA_NAME_PLURAL . ' found',
          'not_found_in_trash' => 'No ' . RECIPE_SCHEMA_LABEL_PLURAL . ' found in trash',
        ),
        '_builtin' => false,
        'exclude_from_search' => true,
        'public' => true,
        'publicly_queryable' => true,
        'has_archive' => true,
        'show_ui' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'query_var' => 'recipes',
        'menu_icon' => RECIPE_SCHEMA_PLUGIN_URL.'img/plate.png',
        'permalink_epmask' => EP_RECIPE,
        'rewrite' => array(
          'slug' => 'recipes',
        ),
        'supports' => array(
          'title',
          'thumbnail'
        ),
        //      'rewrite' => array( 'slug' => 'zombie', 'with_front' => FALSE),
        //'register_meta_box_cb'=>'add_meta_boxes',
      )
    );
    $this->add_endpoints();
  }

  public function import_post_page_save() {
    $tax_input = isset( $_POST['tax_input'] ) ? $_POST['tax_input'] : '';
    $recipe = array(
      'post_title' => wp_strip_all_tags( $_POST['title'] ),
      'post_content' => '',
      'post_status' => 'publish',
      'post_type' => RECIPE_SCHEMA_POSTTYPE,
      'tax_input' => $tax_input
    );

    $post_id = $_POST['post_id'];
    $recipe_id = wp_insert_post( $recipe );
    if ( $recipe_id ) {
      if ( isset( $_POST['featured_image_id'] ) ) {
        set_post_thumbnail( $recipe_id, $_POST['featured_image_id'] );
      }
      $updated_post = array();
      $updated_post['ID'] = $post_id;
      $updated_post['post_content'] = stripslashes( $_POST['content'] );

      // Update the post into the database
      wp_update_post( $updated_post );
      $this->save_recipe( $recipe_id );

      $recipe_list = get_post_meta( $post_id, 'recipe_ids', TRUE );
      if ( !is_array( $recipe_list ) ) {
        $recipe_list = array();
      }
      $recipe_list[] = $recipe_id;
      $this->save_post( $post_id, $recipe_list, '' );
      $this->client->import_post_page_review( $recipe_id, $post_id );
    }
  }

  public function paste_import_save() {
    $tax_input = isset( $_POST['tax_input'] ) ? $_POST['tax_input'] : '';
    $recipe = array(
      'post_title' => wp_strip_all_tags( $_POST['title'] ),
      'post_content' => '',
      'post_status' => 'publish',
      'post_type' => RECIPE_SCHEMA_POSTTYPE,
      'tax_input' => $tax_input
    );

    $post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : '';

    $recipe_id = wp_insert_post( $recipe );
    if ( $recipe_id ) {
      $this->save_recipe( $recipe_id );
      $this->client->import_post_page_review( $recipe_id, $post_id );
    }
  }

  public function import_post_page() {

    if ( isset( $_REQUEST['action'] ) ) {
      if ( $_REQUEST['action'] == 'text_selected' ) {
        $this->client->import_post_page_tag_text();
      }
      if ( $_REQUEST['action'] == 'text_tagged' ) {
        $this->import_post_page_save();
      }
      if ( $_REQUEST['action'] == 'post_selected' ) {

        $this->client->import_post_page_select_text();
      }
    } else {
      $this->client->import_post_select();
    }
  }

  public function paste_import() {
    if ( isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'text_tagged' ) ) {
      $this->paste_import_save();
    } else {
      $this->client->paste_import();
    }
  }


  public function menu_pages() {
    global $submenu;

    add_submenu_page( 'edit.php?post_type=' . RECIPE_SCHEMA_POSTTYPE, 'Import from Posts', 'Import from Post', 'manage_options', 'import_post', array( $this, 'import_post_page' ) );
    add_submenu_page( 'edit.php?post_type=' . RECIPE_SCHEMA_POSTTYPE, 'Copy, Paste & Import', 'Copy, Paste & Import', 'manage_options', 'paste_import', array( $this, 'paste_import' ) );
    add_options_page( 'Recipe Settings', 'Recipe Schema', 'manage_options', RECIPE_SCHEMA_POSTTYPE, array( $this->client, 'settings_page' ) );


    $menu_page = 'edit.php?post_type=recipe-schema';

    // This needs to be set to the URL for the admin menu option to remove (aka "Submenu Page")
    $cusisine_submenu_page = 'edit-tags.php?taxonomy=recipe_type&amp;post_type=recipe-schema';
    $recipe_type_submenu_page = 'edit-tags.php?taxonomy=cuisine&amp;post_type=recipe-schema';

    // This removes the menu option but doesn't disable the taxonomy
    foreach ( $submenu[$menu_page] as $index => $submenu_item ) {
      if ( ( $submenu_item[2]==$cusisine_submenu_page ) || ( $submenu_item[2]==$recipe_type_submenu_page ) ) {
        unset( $submenu[$menu_page][$index] );
      }
    }

  }

  public function add_custom_box() {
    add_meta_box(
      'recipe_schema_ingredient_box',
      'Ingredients',
      array( $this->client, 'inner_ingredient_box' ),
      RECIPE_SCHEMA_POSTTYPE
    );

    add_meta_box(
      'recipe_schema_direction_box',
      'Directions',
      array( $this->client, 'inner_direction_box' ),
      RECIPE_SCHEMA_POSTTYPE
    );

    add_meta_box(
      'recipe_schema_meta_box',
      'Optional',
      array( $this->client, 'inner_recipe_form_box' ),
      RECIPE_SCHEMA_POSTTYPE
    );


    add_meta_box(
      'recipe_schema_list_box',
      'Recipe Schema',
      array( $this->client, 'inner_list_box' ),
      'post'
    );

    add_meta_box(
      'recipe_schema_notes_box',
      'Notes',
      array( $this->client, 'inner_notes_box' ),
      RECIPE_SCHEMA_POSTTYPE
    );

    add_meta_box(
      'recipe_schema_equipment_box',
      'Equipment',
      array( $this->client, 'inner_equipment_box' ),
      RECIPE_SCHEMA_POSTTYPE
    );

  }


  public function get_posts( $posts ) {
    global $wpdb;

    $updated_posts = array();
    foreach ( $posts as $post ) {

      if ( $post->post_type == 'post' ) {
        $content = $post->post_content;
        if ( strpos( $content, '[recipes' ) === false ) {

          $recipe_list = get_post_meta( $post->ID, 'recipe_ids', TRUE );

          if ( $recipe_list ) {
            $querystr = "
                  SELECT $wpdb->posts.post_title, $wpdb->posts.ID
                  FROM $wpdb->posts
                  WHERE  $wpdb->posts.post_type = '" . RECIPE_SCHEMA_POSTTYPE . "'
                  AND  $wpdb->posts.ID IN (" . implode( ",", $recipe_list ) . ")";

            $recipes_array = $wpdb->get_results( $querystr, OBJECT );

            foreach ( $recipes_array  as $row ) {
              $content = $content .  $this->client->print_recipe( $row, $post->ID );

            }
            $post->post_content = $content;
          }
        }
      }
      if ( $post->post_type == RECIPE_SCHEMA_POSTTYPE ) {

        $post->post_content = $post->post_content . $this->client->print_recipe( $post, $post->ID );


      }
      $updated_posts[] = $post;

    }
    return $updated_posts;
  }


  public function post_delete( $post_id ) {

    $post = get_post( $post_id, 'OBJECT' );
    if ( $post->post_type == 'post' ) {
      $recipe_list = get_post_meta( $post_id, 'recipe_ids', TRUE );
      foreach ( $recipe_list  as $recipe_id ) {
        $this->remove_postid( $post_id, $recipe_id );
      }
    }
    if ( $post->post_type == RECIPE_SCHEMA_POSTTYPE ) {
      $this->remove_recipeid_from_all_posts( $recipe_id );
    }
  }

  public function revert_post( $post_id, $recipe_list ) {
    $post = get_post( $post_id, 'OBJECT' );
    $post_content = $post->post_content;

    //$content = apply_filters('the_content', $post->post_content );
    $recipe_list = get_post_meta( $post->ID, 'recipe_ids', TRUE );
    $recipe_content = '';
    if ( $recipe_list ) {
      $querystr = "
                SELECT $wpdb->posts.post_title, $wpdb->posts.ID
                FROM $wpdb->posts
                WHERE  $wpdb->posts.post_type = '" . RECIPE_SCHEMA_POSTTYPE . "'
                AND  $wpdb->posts.ID IN (" . implode( ",", $recipe_list ) . ")";

      $recipes_array = $wpdb->get_results( $querystr, OBJECT );

      foreach ( $recipes_array  as $row ) {
        $recipe_content = $recipe_content .  $this->client->print_recipe( $row, $post_id );

      }
    }


    $pos = strpos( $content, '[recipes]' );
    if ( $pos === false ) {

      $post_content = $post_content . $content;
    }
    else {

      $post_content = substr_replace( $post_content, $recipe_content, $pos, strlen( '[recipes]' ) );

    }




    $updated_post = array();
    $updated_post['ID'] = $post_id;
    $updated_post['post_content'] = $post_content;

    // Update the post into the database
    wp_update_post( $updated_post );


  }
  public function revert_recipe_schema() {
    $querystr = "SELECT $wpdb->postmeta.post_id, $wpdb->postmeta.meta_value
               FROM $wpdb->postmeta
               WHERE  $wpdb->postmeta.meta_key = 'recipe_ids' ";

    $recipes_array = $wpdb->get_results( $querystr, OBJECT );

    foreach ( $recipes_array  as $row ) {
      //error_log(print_r($row, true));
      $post_id = $row->post_id;
      $recipe_list = $row->meta_value; //get_post_meta( $recipe_id, 'post_ids', TRUE );
      if ( is_array( $recipe_list ) ) {
        $this->revert_post( $post_id, $recipe_list );

        $key = array_search( $post_id, $post_list );
        unset( $post_list[$key] );
        update_post_meta( $recipe_id, 'post_ids', $post_list );
      }



    }


  }




  public function input_parser( $el ) {
    $test = array();
    switch ( $el->nodeName ) {
    case 'li':
      $test[] = $el->nodeValue;
      return $test; //'Ing: ' . $el->nodeValue;
      break;
    case 'div':
      if ( $el->hasChildNodes() ) {
        $text ='';
        foreach ( $el->childNodes as $i ) {
          //$text = $text . $this->print_ingredients($i) . "\n";
          $test = array_merge( $test, $this->input_parser( $i ) );
        }
        return $test; //"Div: \n" .$text;
      } else {
        $test[] = '=' . $el->nodeValue;
        return $test; //'Header: ' . $el->nodeValue . "\n";
      }
      break;
    case 'h4':
    case 'span':
    case '#text':
      $test[] = '=' . $el->nodeValue;
      return $test; //'Header: ' . $el->nodeValue . "\n";
      break;
    case 'ol':
    case 'ul':
      if ( $el->hasChildNodes() ) {
        $text ='';
        foreach ( $el->childNodes as $i ) {
          //$text = $text . $this->print_ingredients($i) . "\n";
          $test = array_merge( $test, $this->input_parser( $i ) );
        }
        return $test; //"List: \n" . $text;
      } else {
        return $test; //'Empty List: ' . $el->nodeValue . "\n";
      }
    default:
      return $test; //"Empty Element - " . $el->nodeValue . $el->nodeName;
      break;
    }
  }
  public function save_recipe( $post_id ) {
    $ingredients = $_POST["ingredients"];
    $directions = $_POST["directions"];

    $directions_array = array();

    libxml_use_internal_errors( true );
    $dom = new DOMDocument;
    $dom->loadHTML('<?xml encoding="UTF-8">' . $ingredients );

    $items = $dom->documentElement;

    $result = array();
    foreach ( $items->childNodes as $item ) {

      if ( $item->hasChildNodes() ) {
        $childs = $item->childNodes;
        foreach ( $childs as $i ) {
          $result = array_merge( $result, $this->input_parser( $i ) );

        }
      }
    }



    Ingredient::delete_recipe_ingredients( $post_id );

    $lines = $result; //$resulpreg_split( "/[\n\r]+/", $ingredients );
    $count = 1;
    foreach ( $lines as $line ) {
      $ing = Ingredient::process( $line, $count );

      if ( $ing ) {
        $ing->add_to_database( $post_id );
        $count++;
      }
    }


    $dom->loadHTML('<?xml encoding="UTF-8">' .  $directions );

    $items = $dom->documentElement;

    $result = array();
    foreach ( $items->childNodes as $item ) {

      if ( $item->hasChildNodes() ) {
        $childs = $item->childNodes;
        foreach ( $childs as $i ) {
          $result = array_merge( $result, $this->input_parser( $i ) );

        }
      }
    }

    $lines = $result; //preg_split( "/[\n\r]+/", $directions );
    $count = 1;
    foreach ( $lines as $line ) {
      $step = Direction::process( $count, $line );
      if ( $step )
        $directions_array[] = $step;
      $count++;
    }
    update_post_meta( $post_id, 'directions', $directions_array );

    if ( isset( $_POST["recipe_asin"] ) ) {
      $array = explode( ",", wp_strip_all_tags( $_POST["recipe_asin"] ) );
      $array= array_filter( $array );
      update_post_meta( $post_id, 'equipment', implode( ",", $array ) );
    }

    if ( isset( $_POST["notes"] ) ) {
      update_post_meta( $post_id, 'notes', wp_strip_all_tags( $_POST["notes"] ) );
    }

    if ( isset( $_POST["source"] ) ) {
      update_post_meta( $post_id, 'source', wp_strip_all_tags( $_POST["source"] ) );
    }
    if ( isset( $_POST["source-url"] ) ) {
      update_post_meta( $post_id, 'source_url', wp_strip_all_tags( $_POST["source-url"] ) );
    }
    if ( strlen( $_POST["prep-time-hour"] ) || strlen( $_POST["prep-time-min"] ) ) {
      $min = 0 + (int) $_POST["prep-time-hour"] * 60;
      $min = $min + (int) $_POST["prep-time-min"];
      update_post_meta( $post_id, 'prep_time', $min );
    } else {
      update_post_meta( $post_id, 'prep_time', 0 );
    }

    if ( strlen( $_POST["cooking-time-hour"] ) || strlen( $_POST["cooking-time-min"] ) ) {
      $min = 0 + (int) $_POST["cooking-time-hour"] * 60;
      $min = $min + (int) $_POST["cooking-time-min"];
      update_post_meta( $post_id, 'cooking_time', $min );
    } else {
      update_post_meta( $post_id, 'cooking_time', 0 );
    }

    if ( strlen( $_POST["total-time-hour"] ) || strlen( $_POST["total-time-min"] ) ) {
      $min = 0 + (int) $_POST["total-time-hour"] * 60;
      $min = $min + (int) $_POST["total-time-min"];
      update_post_meta( $post_id, 'total_time', $min );
    } else {
      update_post_meta( $post_id, 'total_time', 0 );
    }

    if ( isset( $_POST["yield"] ) ) {
      update_post_meta( $post_id, 'yield', wp_strip_all_tags( $_POST["yield"] ) );
    }
    if ( isset( $_POST["category"] ) ) {
      update_post_meta( $post_id, 'category', wp_strip_all_tags( $_POST["category"] ) );
    }
    if ( isset( $_POST["cuisine"] ) ) {
      update_post_meta( $post_id, 'cuisine', wp_strip_all_tags( $_POST["cuisine"] ) );
    }
  }

  public function remove_recipeid_from_all_posts( $recipe_id ) {
    $post_list = get_post_meta( $recipe_id, 'post_ids', TRUE );

    if ( is_array( $post_list ) ) {
      foreach ( $post_list  as $post_id ) {
        $recipe_list = get_post_meta( $post_id, 'recipe_ids', TRUE );
        $key = array_search( $recipe_id, $recipe_list );
        unset( $recipe_list[$key] );
        update_post_meta( $post_id, 'recipe_ids', $recipe_list );
      }
    }

  }


  public function remove_postid( $post_id, $recipe_id ) {
    $post_list = get_post_meta( $recipe_id, 'post_ids', TRUE );
    if ( is_array( $post_list ) ) {
      $key = array_search( $post_id, $post_list );
      unset( $post_list[$key] );
      update_post_meta( $recipe_id, 'post_ids', $post_list );
    }

  }

  public function add_postid( $post_id, $recipe_id ) {
    $post_list = get_post_meta( $recipe_id, 'post_ids', TRUE );
    $term_list = wp_get_post_terms( $recipe_id, 'recipe_type', array( "fields" => "names" ) );
    $term_list = array_merge( $term_list, wp_get_post_terms( $recipe_id, 'cuisine', array( "fields" => "names" ) ) );
    wp_set_post_terms( $post_id, $term_list, 'post_tag', true );
    $post_list[] = $post_id;
    update_post_meta( $recipe_id, 'post_ids', $post_list );

  }

  public function save_post( $post_id, $recipe_list, $thumb_list ) {
    // OK, we're authenticated: we need to find and save the data

    if ( function_exists( 'wp_enqueue_media' ) && ( count( $recipe_list ) == count( $thumb_list ) ) ) {
      for ( $i=0; $i < count( $thumb_list );++$i ) {
        set_post_thumbnail( $recipe_list[$i], $thumb_list[$i] );
      }
    }

    $old_recipe_list = get_post_meta( $post_id, 'recipe_ids', TRUE );
    if ( !is_array( $old_recipe_list ) ) {
      $old_recipe_list = array();
    }
    if ( isset( $recipe_list ) ) {
      $new_recipe_list = $recipe_list;
    } else {
      $new_recipe_list = array();
    }


    $deleted_recipes = array_diff( $old_recipe_list, $new_recipe_list );
    $added_recipes = array_diff( $new_recipe_list, $old_recipe_list );
    foreach ( $deleted_recipes  as $recipe_id ) {
      $this->remove_postid( $post_id, $recipe_id );
    }

    foreach ( $added_recipes  as $recipe_id ) {
      $this->add_postid( $post_id, $recipe_id );
    }
    update_post_meta( $post_id, 'recipe_ids', $new_recipe_list );

  }

  public function save_postdata( $post_id, $post ) {
    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;
    if ( $post->post_type == 'revision' )
      return;
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    /*  if ( !wp_verify_nonce( $_POST['silly_monkey'], plugin_basename( __FILE__ ) ) )
    {

        return;
    }*/

    // Check permissions
    if ( isset( $_POST['post_type'] ) ) {
      if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
          return;
      }
      else {
        if ( !current_user_can( 'edit_post', $post_id ) )
          return;
      }

      if ( $_POST['post_type'] == RECIPE_SCHEMA_POSTTYPE ) {
        $this->save_recipe( $post_id );
      }
      if ( $_POST['post_type'] == 'post' ) {
        if ( isset( $_POST['recipe_id'] ) ) {
          $recipe_list = $_POST['recipe_id'];
          $thumb_list = $_POST['recipe_thumbnail_id'];
        } else {
          $recipe_list = array();
          $thumb_list = array();
        }

        $this->save_post( $post_id, $recipe_list, $thumb_list );
      }

    }
  }

}
$recipe_schema = new Recipe_Schema();


?>
