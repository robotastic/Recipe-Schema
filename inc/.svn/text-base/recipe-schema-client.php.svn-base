<?php

require_once 'recipe-import-post-list.php';
require_once 'post-recipe-list.php';


require_once RECIPE_SCHEMA_PLUGINDIR . '/templates/default.php';



class Recipe_Schema_Client {
  private $amazon;


  public function __construct( $amazon = null ) {
    $this->amazon = $amazon;
  }


  public function settings_page() {


    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';


    if ( isset( $_GET['updated'] ) ) : ?>
 <div id="message" class="updated"><p><?php _e( 'File edited successfully.' ) ?></p></div>
<?php endif;

    if ( $active_tab == 'template' ) {
      $error = false;
      if ( empty( $_POST['action'] ) ) {
        $action = 'read';
      } else {
        $action = $_POST['action'];
      }
      if ( empty( $_POST['file'] ) ) {
        if ( empty( $_GET['file'] ) ) {
          $filename = 'default.php';
        } else {
          $file_input = pathinfo( $_GET['file'] );
          $filename = $file_input['basename'];
        }
      } else {
        $file_input = pathinfo( $_POST['file'] );
        $filename = $file_input['basename'];
      }
      $file = RECIPE_SCHEMA_PLUGINDIR . '/templates/' . $filename;
      $files = glob( RECIPE_SCHEMA_PLUGINDIR . '/templates/*.php' );

      switch ( $action ) {
      case 'update':
        //check_admin_referer( 'edit-theme_' . $file . $stylesheet );
        $newcontent = stripslashes( $_POST['newcontent'] );
        $content = esc_textarea( $newcontent );
        $location = 'options-general.php?page=recipe-schema&tab=template';
        if ( is_writeable( $file ) ) {
          //is_writable() not always reliable, check return value. see comments @ http://uk.php.net/is_writable
          $f = fopen( $file, 'w+' );
          if ( $f !== false ) {
            fwrite( $f, $newcontent );
            fclose( $f );
            $location .= '&updated=true';
            ?><div id="message" class="updated"><p><?php _e( 'File edited successfully.' ) ?></p></div><?php
          }
        }
        //wp_redirect( $location );
        //exit;
        break;

      default:

        if ( ! is_file( $file ) )
          $error = true;

        $content = '';
        if ( ! $error && filesize( $file ) > 0 ) {
          $f = fopen( $file, 'r' );
          $content = fread( $f, filesize( $file ) );
          $content = esc_textarea( $content );
        }
        break;
      }
    } ?>
<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
<h2>Recipe Schema Settings</h2>
  <h2 class="nav-tab-wrapper">
    <a href="?page=recipe-schema&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
    <a href="?page=recipe-schema&tab=template" class="nav-tab <?php echo $active_tab == 'template' ? 'nav-tab-active' : ''; ?>">Template</a>
  </h2>


  <?php if ( $active_tab == 'general' ) {  ?>

    <form method="post" action="options.php">
      <?php settings_fields( 'recipe-schema-settings-group' ); ?>
    <table class="form-table">

         <tr valign="top">
         <th scope="row">Show Recipe Photo</th>
         <td> <fieldset><label for="show_recipe_photo">
         <input name="show_recipe_photo" type="checkbox" id="show_recipe_photo" value="1" <?php checked( '1', get_option( 'show_recipe_photo' ) ); ?> /></label>
         </fieldset></td>
         </tr>
         <tr valign="top">
         <th scope="row">Show Print Button</th>
         <td> <fieldset><label for="show_print_button">
         <input name="show_print_button" type="checkbox" id="show_print_button" value="1" <?php checked( '1', get_option( 'show_print_button' ) ); ?> /></label>
         </fieldset></td>
         </tr>
         <tr valign="top">
         <th scope="row">Show Facebook Button</th>
         <td> <fieldset><label for="show_facebook_button">
         <input name="show_facebook_button" type="checkbox" id="show_facebook_button" value="1" <?php checked( '1', get_option( 'show_facebook_button' ) ); ?> /></label>
         </fieldset></td>
         </tr>
    </table>
    <h3>Whisk</h3>
    <p><img src="<?php echo RECIPE_SCHEMA_PLUGIN_URL.'img/whisk_button_small.png'?>"/></p>
    <p>The Whisk Button allows your readers to purchase the ingredients of recipes instantly. It matches the recipe ingredients to store items from supermarkets and in few clicks, you can arrange for them to be delivered to you. To get more information about Whisk and you can benefit from adding the button to your website visit <a href="http://www.whisk.co.uk/">whisk.com</a>. If you decide to enable the Whisk button on your website contact us under <a href="hello@whisk.co.uk">hello@whisk.co.uk</a></p>
    <table class="form-table">
         <tr valign="top">
         <th scope="row">Show Whisk Button</th>
         <td> <fieldset><label for="show_whisk_button">
         <input name="show_whisk_button" type="checkbox" id="show_whisk_button" value="1" <?php checked( '1', get_option( 'show_whisk_button' ) ); ?> /></label>
         </fieldset></td>
         </tr>
    </table>     
    <h3>Amazon Associates</h3>
    <p>Amazon Associates is an affliate program that gives a percentage of the sales of products your visitors buy. After you enter this information, you can find and add Amazon items that relate to a recipe and have them linked to your Amazon Associates account.</p>
    <p>In order to do this you need to sign up for an <A href="https://affiliate-program.amazon.com/">Amazon Associates</a> account, then enable the <a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html">Product API</a> and finally sign up for an <a href="http://aws.amazon.com/">Amazon Web Services</a> account.</p>
    <table class="form-table">
         <tr valign="top">
         <th scope="row">Amazon API Key</th>
         <td> <fieldset><label for="amazon_key">
         <input name="amazon_key" type="text" id="amazon_key" value="<?php echo get_option( 'amazon_key' ); ?> "/></label>
         </fieldset><p class="description">The Access Key ID for your AWS account, available <a href="https://portal.aws.amazon.com/gp/aws/securityCredentials">here</a></p></td>

         </tr>
         <tr valign="top">
         <th scope="row">Amazon Secret Key</th>
         <td> <fieldset><label for="amazon_secret">
         <input name="amazon_secret" type="text" id="amazon_secret" value="<?php echo get_option( 'amazon_secret' ); ?> "/></label>
         </fieldset><p class="description">The Secret Key for your AWS account, available <a href="https://portal.aws.amazon.com/gp/aws/securityCredentials">here</a></p></td>
</td>
         </tr>
         <tr valign="top">
         <th scope="row">Amazon Associates ID</th>
         <td> <fieldset><label for="amazon_associate">
         <input name="amazon_associate" type="text" id="amazon_associate" value="<?php echo get_option( 'amazon_associate' ); ?> "/></label>
         </fieldset><p class="description">Your Amazon Associates ID</p></td>
         </tr>

    </table>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    </p>
    <a class='button-secondary' href='<?php echo site_url();?>/wp-admin/edit-tags.php?taxonomy=recipe_type&post_type=recipe-schema' title='Recipe Type Taxonomy'>Recipe Type Taxonomy</a>
    <a class='button-secondary' href='<?php echo site_url();?>/wp-admin/edit-tags.php?taxonomy=cuisine&post_type=recipe-schema' title='Cuisine Taxonomy'>Cuisine Taxonomy</a>
    </form>
      <?php } else { ?>
       <div class="fileedit-sub">

        <div class="align-left"><h3><?php echo $filename ?> </h3></div>
        <br class="clear" />
      </div>

               <div id="templateside">
            <h3><?php _e( 'Templates' ); ?></h3>
            <ul>
            <?php
      foreach ( $files as $f ) {

        $file_input = pathinfo( $f );
        $f = $file_input['basename'];
?>
<li><a href="options-general.php?page=recipe-schema&tab=template&file=<?php echo urlencode( $f ) ?>"><?php echo $f; ?></a></li>


            <?php
      }


?>
          </ul>

          </div>

        <form method="post" id="template"action="options-general.php?page=recipe-schema&tab=template">
          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="file" value="<?php echo $filename; ?>" />
          <div >
          <textarea cols="70" rows="30" name="newcontent" id="newcontent" tabindex="1"><?php echo $content ?></textarea>
          </div>
          <div>
          <p class="submit">
          <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
          </p>
        </div>
        </form>
        <p>Note: There is a good chance your changes to the template will be overwritten if you update Recipe Schema. It is probably a good idea to save a copy locally.</p>

        <?php } ?>
</div>
<?php
  }


  function add_open_graph_tags() {
    if ( is_single() ) {
      global $post;
      $recipe_list = get_post_meta( $post->ID, 'recipe_ids', TRUE );
      if ( $recipe_list ) {
        $recipe_id = $recipe_list[0];
        $recipe = get_post( $recipe_id, 'OBJECT' );


        if ( has_post_thumbnail( $recipe_id ) ) {
          $post_thumbnail_id = get_post_thumbnail_id( $recipe_id );
          $image = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );
        }



?>
    <meta property="fb:app_id" content="475278205818883" />
    <meta property="og:type"   content="mr_cookbook:recipe" />
    <meta property="og:title" content="<?php echo $recipe->post_title ?>" />
    <meta property="og:image" content="<?php echo $image[0]; ?>" />
    <meta property="og:url" content="<?php echo the_permalink(); ?>" />
    <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>" />
    <?php
      }

    }

  }


  public function print_time( $time ) {
    $hours = floor( (int) $time / 60 );
    $min = (int) $time % 60;

    $result = '';
    if ( $hours == 1 ) {
      $result = $result . $hours . ' Hour ';
    }
    if ( $hours > 1 ) {
      $result = $result . $hours . ' Hours ';
    }
    if ( $min == 1 ) {
      $result = $result . $min . ' Min ';
    }
    if ( $min > 1 ) {
      $result = $result . $min . ' Mins ';
    }

    return $result;
  }

  public function json_print_time( $title, $time ) {
    $hours = floor( (int) $time / 60 );
    $min = (int) $time % 60;

    $result =  '"'.$title . '": "';
    if ( $hours )
      $result = $result . $hours . ' H ';
    $result = $result . $min . ' M",';
    return $result;
  }


  public function recipe_text( $recipe ) {
    global $wpdb;

    $sql = "SELECT * FROM $wpdb->recipe_schema_ingredients
          WHERE $recipe->ID = recipe_id ORDER BY step ASC";
    $item = array( 'title' => $recipe->post_title );

    $item['ingredients'] = $wpdb->get_results( $sql );
    $item['directions'] = get_post_meta( $recipe->ID, 'directions', TRUE );


    $source = get_post_meta( $recipe->ID, 'source', TRUE );
    if ( strlen( $source ) ) {
      $item['source'] = $source;
      $source_url = get_post_meta( $recipe->ID, 'source_url', TRUE );
      if ( strlen( $source_url ) ) {
        $item['source_url'] = $source_url;
      }

    }

    $prep_time = get_post_meta( $recipe->ID, 'prep_time', TRUE );
    $cooking_time = get_post_meta( $recipe->ID, 'cooking_time', TRUE );
    $total_time = get_post_meta( $recipe->ID, 'total_time', TRUE );
    $notes = get_post_meta( $recipe->ID, 'notes', TRUE );

    if ( $prep_time )
      $item['prep_time'] = $this->print_time( $prep_time );
    if ( $cooking_time )
      $item['cooking_time'] = $this->print_time( $cooking_time );
    if ( $total_time )
      $item['total_time'] = $this->print_time( $total_time );
    /*
  $item['yield'] = get_post_meta($recipe->ID,'yield', TRUE);
  $item['category'] = get_post_meta($recipe->ID,'category', TRUE);
  $item['cuisine'] = get_post_meta($recipe->ID,'cuisine', TRUE);  */


    //return include(RECIPE_SCHEMA_PLUGINDIR . '/templates/default.php'  );//$twig->render('default.twig', $item);
    return recipe_text( $item );
  }

  public function recipe_equipment( $recipe) {
    $result = "<div id='equipment_items'>"; 
    if ( $this->amazon ) {
      $equipment = get_post_meta( $recipe->ID, 'equipment', TRUE );
      $items = $this->amazon->cached_query( $equipment );
      if (count($items) !=0 ) {
       $result = $result . "<p><b>Equipment:</b></p>";
      }
      foreach ( $items as $item ) {
        $result = $result . "<div id='" . $item['asin'] . "' class='equipment_item'><a href='" . urldecode($item['url']) . "'><img src='" . $item['image'] . "'></a><br>";
        $result = $result . "<a href='" . urldecode($item['url']) . "'>" . $item['title'] . "</a><br>";
        if ( isset( $item['price'] ) )
          $result = $result . $item['price'] . "<br>";
        $result = $result . "</div>";
      }
    }
    return $result . "</div>";

  }
  public function print_recipe( $recipe, $id ) {
    // <a href="#" onclick="print_recipe(\'' . $print_link . '\')" target="_blank">


    $result = '<div itemscope itemtype="http://schema.org/Recipe" class="recipe">';
    $print_link = get_permalink( $recipe->ID ) . '/print';
    $json_link = get_permalink( $recipe->ID ) . '/json';

    if ( get_option( 'show_whisk_button' ) ) {
      $result = $result . '<div class="recipe-extra" style="float:right;"><script async="true" src="https://www.whisk.co.uk/app/assets/whiskbutton.js" type="text/javascript"></script><a class="whisk-button" style="display:none;"><img src="'.RECIPE_SCHEMA_PLUGIN_URL.'img/whisk_button_small.png"/></a></div>';
    }

    if ( get_option( 'show_facebook_button' ) && $id ) {
      $result = $result . '<div class="recipe-extra" style="float:right;padding-right: 10px;"><iframe frameborder="0" height="22px" width="80px" src="http://mistercookbook.com/button?json=' . $json_link . '&post_url='. get_permalink( $id )  .'&post='.urlencode( $recipe->post_title ).'"></iframe></div>';
    }
    if ( get_option( 'show_print_button' ) ) {
      $result = $result . '<div class="recipe-extra" style="float:right;padding-right: 10px;"><a href="' . $print_link . '" target="_blank"><img title="Print Recipe" src="'.RECIPE_SCHEMA_PLUGIN_URL.'img/print.gif"></a></div>';
    }



    if ( has_post_thumbnail( $recipe->ID ) ) {
      if ( get_option( 'show_recipe_photo' ) ) {
        $result = $result . '<div>'.  get_the_post_thumbnail( $recipe->ID, 'large', array( 'itemprop' => 'image' ) ) . '</div>';
      } else {
        $result = $result . '<div>'.   get_the_post_thumbnail( $recipe->ID, 'large', array( 'style' => 'display: none', 'itemprop' => 'image' ) ) . '</div>';
      }
    }
    $result = $result . $this->recipe_text( $recipe );
    $result = $result . $this->recipe_equipment( $recipe );
    $result = $result . '</div>';

    return $result;
  }

  function recipe_json( $recipe ) {
    global $wpdb;

    $sql = "SELECT * FROM $wpdb->recipe_schema_ingredients
          WHERE $recipe->ID = recipe_id ORDER BY step ASC";
    $ingredients = $wpdb->get_results( $sql );

    $recipe_json = array( "recipe" =>
      array( "title" => $recipe->post_title,
      ) );
    // Title
    $result = '{ "recipe" : { ';
    $result = $result . '"title" : "' . $recipe->post_title . '",';


    if ( has_post_thumbnail( $recipe->ID ) ) {
      $post_thumbnail_id = get_post_thumbnail_id( $recipe->ID );
      $image = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );

      $result = $result . '"thumbnail_url": "'.  $image[0]  . '",';
      $recipe_json["recipe"]["thumbnail_url"] = $image[0];
    }

    if ( has_post_thumbnail( $recipe->ID ) ) {
      $post_thumbnail_id = get_post_thumbnail_id( $recipe->ID );
      $image = wp_get_attachment_image_src( $post_thumbnail_id, 'medium' );

      $result = $result . '"image_url": "'.  $image[0]  . '",';
      $recipe_json["recipe"]["image_url"] = $image[0];
    }

    // Source
    $source = get_post_meta( $recipe->ID, 'source', TRUE );
    if ( strlen( $source ) ) {
      $source_url = get_post_meta( $recipe->ID, 'source_url', TRUE );
      $result = $result . '"source" : { "title" : "' . $source . '"';
      if ( strlen( $source_url ) ) {
        $result = $result . ', "url": "' . $source_url .'"},';
        $recipe_json["recipe"]["source"] = array( "title" => $source, "url" => $source_url );
      }
      else {
        $result = $result . '},';
        $recipe_json["recipe"]["source"] = array( "title" => $source, "url" => $source_url );
      }
    }

    $prep_time = get_post_meta( $recipe->ID, 'prep_time', TRUE );
    $cooking_time = get_post_meta( $recipe->ID, 'cooking_time', TRUE );
    $total_time = get_post_meta( $recipe->ID, 'total_time', TRUE );
    $notes = get_post_meta( $recipe->ID, 'notes', TRUE );


    //cooking times
    if ( $prep_time ) {
      $result = $result . $this->json_print_time( "prep_time", $prep_time );
      $recipe_json["recipe"]["prep_time"] = $prep_time;
    }
    if ( $cooking_time ) {
      $result = $result . $this->json_print_time( "cooking_time", $cooking_time );
      $recipe_json["recipe"]["cooking_time"] = $cooking_time;
    }
    if ( $total_time ) {
      $result = $result . $this->json_print_time( "total_time", $total_time );
      $recipe_json["recipe"]["total_time"] = $total_time;
    }


    $yield = get_post_meta( $recipe->ID, 'yield', TRUE );
    $category = get_post_meta( $recipe->ID, 'category', TRUE );
    $cuisine = get_post_meta( $recipe->ID, 'cuisine', TRUE );

    if ( $yield ) {
      $result = $result . '"yield": "' . $yield . '",';
      $recipe_json["recipe"]["yield"] = $yield;
    }
    if ( $category ) {
      $result = $result . '"category": "' . $category . '", ';
      $recipe_json["recipe"]["category"] = $category;
    }
    if ( $cuisine ) {
      $result = $result . '"cuisine": "' . $cuisine . '", ';
      $recipe_json["recipe"]["cuisine"] = $cuisine;
    }

    $ing = array();
    if ( $ingredients ) {
      $result = $result . '"ingredients": [';

      $lines = count( $ingredients );
      $i=1;
      foreach ( $ingredients as $ingredient ) {
        if ( $ingredient->header ) {
          $result = $result . '{ "header": "' . stripslashes( $ingredient->line ) . '" } ';
          $ing[] = array( "header" => stripslashes( $ingredient->line ) );
        } else {
          $result = $result . '{ "ingredient": "' . stripslashes( $ingredient->line ) . '" }';
          $ing[] = array( "ingredient" => stripslashes( $ingredient->line ) );
        }
        if ( $i != $lines ) {
          $result = $result . ',';
        }
        $i++;
      }
      $recipe_json["recipe"]["ingredients"] = $ing;

      $result = $result . '],';
    }
    $directions = get_post_meta( $recipe->ID, 'directions', TRUE );

    $dir = array();
    if ( $directions ) {
      $result = $result . '"directions": [';

      $lines = count( $directions );
      $i=1;
      foreach ( $directions as $direction ) {

        if ( $direction[0] == "=" ) {
          $result = $result . '{ "header": "' . stripslashes( substr( $direction, 1, strlen( $direction )-1 ) ) . '"} ';
          $dir[] = array( "header" => stripslashes( substr( $direction, 1, strlen( $direction )-1 ) ) );

        } else {
          $result = $result . '{ "direction": "' . stripslashes( $direction ) . '"}';
          $dir[] = array( "direction" => stripslashes( $direction ) );
        }
        if ( $i != $lines ) {
          $result = $result . ',';
        }
        $i++;
      }
      $recipe_json["recipe"]["directions"] = $dir;

      $result = $result . '],';
    }
    if ( $notes ) {
      $result = $result . '"note": "' . $notes . '",';
      $recipe_json["recipe"]["note"] = $notes;
    }



    $recipe_json["recipe"]["blog"] = get_bloginfo( 'name' );
    $recipe_json["recipe"]["blog_url"] = site_url();

    $result = $result . '"blog": "' . get_bloginfo( 'name' ) . '",';
    $result = $result . '"blog_url": "' . site_url() . '"';


    $result = $result . ' } }';
    //return $result;
    return json_encode( $recipe_json );
  }

  public function add_script_config() {

    global $post;
?>
<style type="text/css" media="screen">

    #icon-edit.icon32-posts-recipe-schema {background: url('<?php echo RECIPE_SCHEMA_PLUGIN_URL  ?>img/meal.png') no-repeat;}
    </style>
    <?php
    if ( isset( $post ) ) {
      echo '<script type="text/javascript">';
      echo 'postId = ' . $post->ID . ';';
      echo '</script>';
    }
  }

  function json_template_redirect() {
    global $wp_query;
    global $post;


    // if this is not a request for json or a singular object then bail
    if ( ! isset( $wp_query->query_vars['json'] ) || ! is_singular() )
      return;


    echo $this->recipe_json( $post );



    exit;
  }

  function email_template_redirect() {
    global $wp_query;
    global $post;


    // if this is not a request for json or a singular object then bail
    if ( ! isset( $wp_query->query_vars['email'] ) || ! is_singular() )
      return;


    echo $this->print_recipe( $post );



    exit;
  }

  function print_template_redirect() {
    global $wp_query;
    global $post;

    // if this is not a request for json or a singular object then bail
    if ( ! isset( $wp_query->query_vars['print'] ) || ! is_singular() )
      return;
    echo '
    <!DOCTYPE html>
    <html>
    <head>
    <meta name="robots" content="noindex" />
    <meta name="robots" content="nofollow" />
    <link rel="stylesheet" type="text/css" media="all" href="'. get_stylesheet_uri() .'" />
    </head><body>';

    echo '<div ';
    post_class();
    echo '><h1>' . get_bloginfo( "name" ) . '</h1><p>' . get_permalink( $post->ID ) . '</p><div class="recipe">';
    echo $this->recipe_text( $post );

    echo '</div></div>';
    echo '<script type="text/javascript">window.onload = window.print;</script>';
    echo '</body></html>';

    exit;
  }


  function no_recipes_posts_join( $clause='' ) {
    global $wpdb;

    // We join the postmeta table so we can check the value in the WHERE clause.
    $clause .= " LEFT JOIN $wpdb->postmeta AS my_postmeta ON ($wpdb->posts.ID = my_postmeta.post_id AND my_postmeta.meta_key = 'recipe_ids') ";

    return $clause;
  }

  function no_recipes_posts_where( $clause='' ) {
    global $wpdb;

    // Check whether the value is false or NULL. If it is neither, then we want to filter it.
    $clause .= " AND ( (my_postmeta.meta_key = 'recipe_ids' AND CAST(my_postmeta.meta_value AS CHAR) = '') OR my_postmeta.meta_id IS NULL ) ";

    return $clause;
  }

  public function import_post_select() {

    if (  isset( $_GET['no_recipes'] ) ) {
      add_filter( 'posts_join', array( $this, 'no_recipes_posts_join' ) );
      add_filter( 'posts_where', array( $this, 'no_recipes_posts_where' ) );
    }
    $post_list = new Recipe_Schema_Import_Post_List_Table();
    $post_list->prepare_items();
?>
<div class="wrap">
  <div id="icon-edit-pages" class="icon32"></div><h2>Select Post &amp; Import Recipe</h2>
<div class="instruction">Select a previous <strong>Post</strong> that has a recipe in it which you would like to Import. This will create a new <strong>Recipe Post</strong> which will be included in the original post.</div>

  <form id="posts-filter" action="" method="get">
    <!--<input type="hidden" name="action" value="post_selected" />-->
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <input type="hidden" name="post_type" value="<?php echo $_REQUEST['post_type'] ?>" />
    <?php

    $post_list->search_box( 'Search Posts', 'post' );
    $post_list->display();
?>
  </form>
  </div>
<?php
  }
  function remove_all_tinymce_buttons( $items ) {
    $items = array();
    return $items;
  }

  function force_default_editor() {
    //allowed: tinymce, html, test
    return 'tinymce';
  }

  public function import_post_page_select_text() {
    $settings =   array(
      'wpautop' => true, // use wpautop?
      'media_buttons' => false, // show insert/upload button(s)
      'teeny' => false, // output the minimal editor config used in Press This
      'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
      'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
      'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
    );
?>
  <div class="wrap">
  <div id="icon-edit-pages" class="icon32"></div><h2>Select Recipe Text</h2>
  <div class="instruction">Select the <strong>Text</strong> of the recipe you would like to import. It will be <strong>Cut</strong> and <strong>Imported</strong> into a new <strong>Recipe Post</strong>.</div>



  <form id="selectTextForm" method="post" action="">
  <input type="hidden" name="action" value="text_selected" />
  <div id="recipe-text-selection">

  <?php
    $post_id = $_REQUEST['post_id'];
    $post = get_post( $post_id, 'OBJECT' );
    $content = apply_filters( 'the_content', $post->post_content );
    add_filter( 'mce_buttons', array( $this, 'remove_all_tinymce_buttons' ) );
    add_filter( 'wp_default_editor', array( $this, 'force_default_editor' ) );
    wp_editor(  $content, "content", $settings );




    echo '</div>';
    echo '<button type="button" class="button-primary" onclick="selectText()">Cut &amp; Import Selected Text</button>';
    echo '
  <input type="hidden" value="'. $post_id . '" name="post_id"/>
  <input type="hidden" name="selected_text" id="selected_text" value=" "/>
  <input type="hidden" value="'. $_SERVER['HTTP_REFERER'] . '" name="redirect_page"/>

  </form>
  </div>';


  }
  /*
  public function import_post_page_select_text() {
    global $editor_styles;
    $settings =   array(
      'wpautop' => true, // use wpautop?
      'media_buttons' => false, // show insert/upload button(s)
      'teeny' => false, // output the minimal editor config used in Press This
      'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
      'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
      'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
    );
?>
  <div class="wrap">
  <div id="icon-edit-pages" class="icon32"></div><h2>Select Recipe Text</h2>
  <div class="instruction">Select the <strong>Text</strong> of the recipe you would like to import. It will be <strong>Cut</strong> and <strong>Imported</strong> into a new <strong>Recipe Post</strong>.</div>



  <form id="selectTextForm" method="post" action="">
  <input type="hidden" name="action" value="text_selected" />
  <iframe id="content_ifr" src='javascript:""' frameborder="0" allowtransparency="true" title="Rich Text Area Press ALT F10 for toolbar. Press ALT 0 for help." style="width: 100%; height: 223px; display: block;">
  <html>
<head xmlns="http://www.w3.org/1999/xhtml">
<body id="tinymce" class="mceContentBody wp-editor" dir="ltr">
  Does this work
</body></html>
 </iframe>
 <?php
    //echo '<script type="text/javascript">';
    echo '<button type="button" class="button-primary" onclick="selectText()">Cut &amp; Import Selected Text</button>';
    //echo '</script>';
    echo '
  input type="hidden" name="selected_text" id="selected_text" value=" "/>
  <input type="hidden" value="'. $_SERVER['HTTP_REFERER'] . '" name="redirect_page"/>

  </form>
  </div>';


  }*/

  public function import_post_page_review( $recipe_id, $post_id = null ) {


    $recipe_edit_link = get_edit_post_link( $recipe_id );

    $recipe = get_post( $recipe_id );
?>
  <div class='wrap' id='recipe-review'>
  <div id="icon-edit-pages" class="icon32"></div>

  <h2>Finished Recipe<br>
  <?php if ( isset( $_POST['redirect_page'] ) ) {
      echo "<a href='" . $_POST['redirect_page'] . "' class='add-new-h2'>Import more Posts</a>";
    }?>
  <a href='<?php echo $recipe_edit_link ?>' class='add-new-h2'>Edit Recipe</a>
  <?php if ( $post_id ) { ?>
  <a href='<?php echo get_edit_post_link( $post_id ); ?>' class='add-new-h2'>Edit Post</a>
  <a href='<?php echo wp_get_shortlink( $post_id ); ?>' class='add-new-h2'>View Updated Post</a>

  <?php } ?>
  </h2>
  <?php echo $this->print_recipe( $recipe, 0 ); ?>
  </div>
  <?php
  }

  public function print_recipe_form( $recipe_id = NULL ) {
    if ( $recipe_id ) {
      $source = get_post_meta( $recipe_id, 'source', TRUE );
      $source_url = get_post_meta( $recipe_id, 'source_url', TRUE );
      $notes = get_post_meta( $recipe_id, 'notes', TRUE );
      $yield = get_post_meta( $recipe_id, 'yield', TRUE );
      $prep_time = get_post_meta( $recipe_id, 'prep_time', TRUE );
      $cooking_time = get_post_meta( $recipe_id, 'cooking_time', TRUE );
      $total_time = get_post_meta( $recipe_id, 'total_time', TRUE );
      $yield = get_post_meta( $recipe_id, 'yield', TRUE );
      $category = get_post_meta( $recipe_id, 'category', TRUE );
      $cuisine = get_post_meta( $recipe_id, 'cuisine', TRUE );
    } else {
      $source = NULL;
      $source_url = NULL;
      $notes = NULL;
      $yield = NULL;
      $prep_time = NULL;
      $cooking_time = NULL;
      $total_time = NULL;
      $yield = NULL;
      $category = NULL;
      $cuisine = NULL;
    }

?>
<div id="optional">
<table class="form-table">
<tbody><tr valign="top">
<th scope="row"><label for="source">Source / Author</label></th>
<td><input type="text" name="source" id="source"  value="<?php echo $source; ?>" class="regular-text"/></td>
</tr>
<tr valign="top">
<th scope="row"><label for="source-url">Source / Author URL</label></th>
<td><input type="text" name="source-url" id="source-url" value="<?php echo $source_url; ?>" class="regular-text"/><br><span class="description">URL for the source</span></td>
</tr>
<tr valign="top">
<th scope="row"><label for="prep-time-hour">Prep Time</label></th>
<td>
  <fieldset>
<input type="text" name="prep-time-hour" id="prep-time-hour" class="time" value="<?php if ( $prep_time ) { echo floor( (int)$prep_time / 60 ); }?>" class="regular-text"/>Hour <br>
<input type="text" name="prep-time-min" id="prep-time-min" class="time" value="<?php if ( $prep_time ) { echo (int)$prep_time % 60;} ?>" class="regular-text"/>Min
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="cooking-time-hour">Cooking Time</label></th>
<td>
  <fieldset>
<input type="text" name="cooking-time-hour" id="cooking-time-hour" class="time" value="<?php if ( $cooking_time ) {echo floor( (int)$cooking_time / 60 ); } ?>" class="regular-text"/>Hour <br>
<input type="text" name="cooking-time-min" id="cooking-time-min" class="time"  value="<?php if ( $cooking_time ) {echo (int)$cooking_time % 60; } ?>" class="regular-text"/>Min
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="total-time-hour">Total Time</label></th>
<td>
  <fieldset>
<input type="text" name="total-time-hour" id="total-time-hour" class="time" value="<?php if ( $total_time ) { echo floor( (int)$total_time / 60 ); } ?>" class="regular-text" />Hour <br>
<input type="text" name="total-time-min" id="total-time-min" class="time"  value="<?php if ( $total_time ) { echo (int)$total_time % 60; } ?>" class="regular-text"/>Min
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="yield">Yields</label></th>
<td><input type="text" name="yield" id="yield" value="<?php echo $yield; ?>" class="regular-text"/><br><span class="description">How much does it make</span></td>
</tr>
</tbody></table>

</div>


<?php
  }

  public function import_post_page_tag_text() {

    $post_id = $_POST['post_id'];
?>
  <div class="wrap" id="recipe-schema-form">
<div id="icon-edit-pages" class="icon32"></div><h2>Tag Recipe Components</h2>
<div class="instruction">Select a part of the recipe (Title, Ingredients, Yield...) and then click the appropriate button to Tag it and copy it to one of the form fields below.</div>

  <form method="post" action="" id="post" name="post">
  <input type="hidden" name="action" value="text_tagged" />
  <div id='selected-text'>
  <?php echo  $_POST['selected_text']; ?>
  </div>

  <div id='tag-buttons'>
  <input type="button" id="title_button" onclick="tagText('title')" value="Title"/>
  <input type="button" id="ingredients-area_button" onclick="tagText('ingredients-area')" value="Ingredient Section"/>
  <input type="button" id="directions-area_button" onclick="tagText('directions-area')" value="Direction Section"/>
  <input type="button" id="source_button" onclick="tagText('source')" value="Source"/>
  <input type="button" id="notes_button" onclick="tagText('notes')" value="Notes"/>
  <input type="button" id="yield_button" onclick="tagText('yield')" value="Yield"/>
  <p class="submit">
  </div>

  <div id="recipe-form">
  <label for="title">Title</label>
  <input type="text" name="title" id="title"  />
  <label for="ingredients">Ingredients</label>
  <div contenteditable="" name="ingredients" id="ingredients-area" class="wysiwyg-area"></div>
  <label for="directions">Directions</label>
  <div contenteditable="" name="directions" id="directions-area" class="wysiwyg-area"></div>
  <script type="text/javascript">recipeTextAreas();</script>

  <?php

    $args = array(
      'post_type' => 'attachment',
      'numberposts' => -1,
      'post_status' => null,
      'post_parent' => $post_id
    );

    $attachments = get_posts( $args );
    if ( $attachments ) {
      echo "<label id='selected-image'>Select Image</label>";
      echo "<div id='image-selector' ><ul>";
      foreach ( $attachments as $attachment ) {
        echo '<li class="selectable-image">';
        $image_attributes = wp_get_attachment_image_src( $attachment->ID );
        //echo wp_get_attachment_image( $attachment->ID, 'thumbnail' );
        echo '<img src="' . $image_attributes[0] . '" width="' . $image_attributes[1] . '" height="' .  $image_attributes[2] . '" id="post-image-' . $attachment->ID . '"  >';
        echo '<p>';
        echo apply_filters( 'the_title', $attachment->post_title );
        echo '</p></li>';
      }
?>
           </ul></div>
           <script  type='text/javascript'>
           jQuery(document).ready(function ($){
                  $(".selectable-image").click(function(event) {
                    $target = jQuery(event.target);
                    jQuery(".selectable-image").css("border", "5px solid #fff");
                    $target.closest("li").css("border", "5px solid #ccc");
                    imageId = /post-image-(\d+)/.exec($target.attr("id"));
                    jQuery("#featured_image_id").val(imageId[1]);
                  } );
                });
           </script>
          <?php
    }

    $this->print_recipe_form( $post_id );
?>
<div class='taxlist'>
<label>Recipe Type</label>
<ul id='checklist'>
<?php wp_terms_checklist( 0, array( 'taxonomy' => 'recipe_type' ) ); ?>
</ul>
</div>
<div class='taxlist'>
<label>Cuisine</label>
<ul id='checklist'>
<?php wp_terms_checklist( 0, array( 'taxonomy' => 'cuisine' ) ); ?>
</ul>
</div>

<?php
    echo '

  <label for="notes">Notes</label>
  <textarea name="notes" id="notes"></textarea>
  </div>
  <input type="hidden" value="'. $post_id . '" name="post_id"/>
  <input type="hidden" value="'. $_POST['redirect_page'] . '" name="redirect_page"/>
  <input type="hidden" value="'. esc_html( $_POST['content'] ) .'" name="content"/>
  <input type="hidden" value="" id="featured_image_id" name="featured_image_id" />
  <input type="hidden" name="selected_text" id="selected_text" value=" "/>
  <input type="submit" class="button-primary" value="Save Changes" />
  </p>
  </form>
  </div>';


  }

  public function paste_import() {


    //add_filter('tiny_mce_before_init', 'myformatTinyMCE' );

    $settings =   array(
      'wpautop' => true, // use wpautop?
      'media_buttons' => false, // show insert/upload button(s)
      'teeny' => false, // output the minimal editor config used in Press This
      'dfw' => false, // replace the default fullscreen with DFW (needs specific css)

      'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
    );?>
  <div class="wrap" id="recipe-schema-form">
  <div id="icon-edit-pages" class="icon32"></div><h2>Copy, Paste &amp; Import</h2>
  <div class="instruction">Copy the recipe you would wish to Import from a different webpage. Paste it into the text area below. Select a part of the recipe (Title, Ingredients, Yield...) and then click the appropriate button to Tag it and copy it to one of the form fields below.</div>

  <form method="post" action="" id="post" name="post">
  <input type="hidden" name="action" value="text_tagged" />
  <div id='recipe-text-selection'>
    <?php
    add_filter( 'mce_buttons', array( $this, 'remove_all_tinymce_buttons' ) );
    add_filter( 'wp_default_editor', array( $this, 'force_default_editor' ) );
    wp_editor( "", "content", $settings );
?>
  </div>

  <div id='tag-buttons'>
  <input type="button" id="title_button" onclick="tagEditorText('title')" value="Title"/>
  <input type="button" id="ingredients-area_button" onclick="tagEditorText('ingredients-area')" value="Ingredient Section"/>
  <input type="button" id="directions-area_button" onclick="tagEditorText('directions-area')" value="Direction Section"/>
  <input type="button" id="source_button" onclick="tagEditorText('source')" value="Source"/>
  <input type="button" id="notes_button" onclick="tagEditorText('notes')" value="Notes"/>
  <input type="button" id="yield_button" onclick="tagEditorText('yield')" value="Yield"/>
  <p class="submit">
  </div>

  <div id="recipe-form">
  <label for="title">Title</label>
  <input type="text" name="title" id="title"  />
  <label for="ingredients">Ingredients</label>
  <div contenteditable="" name="ingredients" id="ingredients-area" class="wysiwyg-area"></div>
  <label for="directions">Directions</label>
  <div contenteditable="" name="directions" id="directions-area" class="wysiwyg-area"></div>
  <script type="text/javascript">recipeTextAreas();</script>

  <?php
    $this->print_recipe_form();

    echo '
  <label for="notes">Notes</label>
  <textarea name="notes" id="notes"></textarea>
  </div>

  <input type="hidden" name="selected_text" id="selected_text" value=" "/>
  <input type="submit" class="button-primary" value="Save Changes" />
  </p>
  </form>
  </div>';


  }

  public function inner_notes_box( $recipe ) {


    // Use nonce for verification
    //wp_nonce_field( plugin_basename( __FILE__ ), 'silly_monkey' );

    // The actual fields for data entry

    echo '<textarea name="notes" id="notes" >';

    $notes = get_post_meta( $recipe->ID, 'notes', TRUE );

    echo $notes;

    echo '</textarea>';
  }

  public function print_amazon_items( $equipment ) {
    if ( $this->amazon ) {
      $items = $this->amazon->cached_query( $equipment );

      foreach ( $items as $item ) {
        echo "<div id='" . $item['asin'] . "' class='amazon_item'><img src='" . $item['image'] . "'><br>";
        echo $item['title'] . "<br>";
        if ( isset( $item['price'] ) )
          echo $item['price'] . "<br>";
        echo "<a href='#' onClick='ROBORemoveEquip()'>Remove</a></div>";
      }
    }

  }

  public function inner_equipment_box( $recipe ) {


    // Use nonce for verification
    //wp_nonce_field( plugin_basename( __FILE__ ), 'silly_monkey' );

    // The actual fields for data entry

    if ( !$this->amazon ) {
      echo "<p>Enter your Amazon Associates information on the Recipe Schema settings page to enable.</p>";
    } else {

      $wait = '<img src=\"' . includes_url() . '/images/wpspin.gif\">';
      echo '<div id="recipe_equipment">';
      $equipment = get_post_meta( $recipe->ID, 'equipment', TRUE );

      if ( $equipment ) {
        $this->print_amazon_items( $equipment );
      }
      echo '</div>';
      echo '<input type="text" name="term" id="search_term" size="30" value="" /> ';
      echo '<select id="amazon_category"><option value="All">All</option>';
      echo '<option value="Apparel">Apparel</option>';
      echo '<option value="Appliances">Appliances</option>';
      echo '<option value="ArtsAndCrafts">Arts and Crafts</option>';
      echo '<option value="Automotive">Automotive</option>';
      echo '<option value="Baby">Baby</option>';
      echo '<option value="Beauty">Beauty</option>';
      echo '<option value="Blended">Blended</option>';
      echo '<option value="Books">Books</option>';
      echo '<option value="Classical">Classical</option>';
      echo '<option value="Collectibles">Collectibles</option>';
      echo '<option value="DigitalMusic">DigitalMusic</option>';
      echo '<option value="Grocery">Grocery</option>';
      echo '<option value="DVD">DVD</option>';
      echo '<option value="Electronics">Electronics</option>';
      echo '<option value="HealthPersonalCare">HealthPersonalCare</option>';
      echo '<option value="HomeGarden">HomeGarden</option>';
      echo '<option value="Industrial">Industrial</option>';
      echo '<option value="Jewelry">Jewelry</option>';
      echo '<option value="KindleStore">KindleStore</option>';
      echo '<option value="Kitchen">Kitchen</option>';
      echo '<option value="LawnAndGarden">LawnAndGarden</option>';
      echo '<option value="Magazines">Magazines</option>';
      echo '<option value="Marketplace">Marketplace</option>';
      echo '<option value="Merchants">Merchants</option>';
      echo '<option value="Miscellaneous">Miscellaneous</option>';
      echo '<option value="MobileApps">MobileApps</option>';
      echo '<option value="MP3Downloads">MP3Downloads</option>';
      echo '<option value="Music">Music</option>';
      echo '<option value="MusicalInstruments">MusicalInstruments</option>';
      echo '<option value="MusicTracks">MusicTracks</option>';
      echo '<option value="OfficeProducts">OfficeProducts</option>';
      echo '<option value="OutdoorLiving">OutdoorLiving</option>';
      echo '<option value="PCHardware">PCHardware</option>';
      echo '<option value="PetSupplies">PetSupplies</option>';
      echo '<option value="Photo">Photo</option>';
      echo '<option value="Shoes">Shoes</option>';
      echo '<option value="Software">Software</option>';
      echo '<option value="SportingGoods">SportingGoods</option>';
      echo '<option value="Tools">Tools</option>';
      echo '<option value="Toys">Toys</option>';
      echo '<option value="UnboxVideo">UnboxVideo</option>';
      echo '<option value="VHS">VHS</option>';
      echo '<option value="Video">Video</option>';
      echo '<option value="VideoGames">VideoGames</option>';
      echo '<option value="Watches">Watches</option>';
      echo '<option value="Wireless">Wireless</option>';
      echo '<option value="WirelessAccessories">WirelessAccessories</option></select>';

      echo '<option value="All">All</option>';

      echo '<input type="hidden" name="recipe_asin" id="recipe_asin" value="' . $equipment . '"/>';
      echo '<input type="submit" name="submit" class="button" id="search_btn" value="Search" />';
      echo '<div id="search_results"></div>';
      echo '<script type="text/javascript">
            var page=1,
            term="",
            category="All";
            var $j = jQuery.noConflict();
            $j("#search_term").keypress(function(e){
                if ( e.which == 13 ) {

                  ROBOEquipSearchSubmit(e);
                }
            });
            $j(window).load(function(){
              wait = "' . $wait . '";
            // this is the ID of your FORM tag
              $j("#search_btn").click(ROBOEquipSearchSubmit);
            });
          </script>';
    }
  }

  public function inner_direction_box( $recipe ) {


    // Use nonce for verification
    //wp_nonce_field( plugin_basename( __FILE__ ), 'silly_monkey' );

    // The actual fields for data entry



    echo '<div  contenteditable=""  name="directions" id="directions-area" class="wysiwyg-area">';

    $directions = get_post_meta( $recipe->ID, 'directions', TRUE );

    if ( $directions ) {
      echo '<div><ol>';
      foreach ( $directions as $direction ) {
        if ( $direction[0] == "=" ) {
          echo "</ol>";
          echo '<h4>' , stripslashes( substr( $direction, 1, strlen( $direction )-1 ) ), '</h4>';
          echo "<ol>";
        } else {
          echo '<li>' , stripslashes( $direction ), '</li>';
        }
      }
      echo '</ol></div>';
    }
    echo '</div>';
    echo '<span class="description">Start each step on a new line. For Section Titles, begin the line with an =</span>';
  }

  public function inner_ingredient_box( $recipe ) {
    global $wpdb;

    // Use nonce for verification
    //wp_nonce_field( plugin_basename( __FILE__ ), 'silly_monkey' );

    // The actual fields for data entry


    echo '<div contenteditable="" name="ingredients" id="ingredients-area" class="wysiwyg-area">';


    $sql = "SELECT * FROM $wpdb->recipe_schema_ingredients
          WHERE $recipe->ID = recipe_id ORDER BY step ASC";
    $ingredients = $wpdb->get_results( $sql );

    if ( $ingredients ) {
      echo '<div><ul>';
      foreach ( $ingredients as $ingredient ) {

        if ( $ingredient->header ) {
          echo "</ul>";
          echo '<h4>' . stripslashes( $ingredient->line ) . '</h4>';
          echo "<ul>";
        } else {
          echo "<li>";
          echo stripslashes( $ingredient->line );
          echo "</li>";
        }


      }
      echo '</ul></div>';
    }
    echo '</div>';


    echo '<script type="text/javascript">recipeTextAreas();</script>';
    echo '<span class="description">Enter each Ingredient on a seperate line. For Section Titles, begin the line with an =</span>';
  }

  public function inner_recipe_form_box( $recipe ) {
    global $wpdb;


    $this->print_recipe_form( $recipe->ID );
  }



  public function inner_list_box( $post ) {
    global $wpdb;


    $recipe_list = get_post_meta( $post->ID, 'recipe_ids', TRUE );

    //echo '<div id="icon-edit-pages" class="icon32"<br/></div><h2>Included Recipes<h2>';

    $recipe_list = new Post_Recipe_List_Table( $post->ID, $recipe_list );
    $recipe_list->prepare_items();
?>
<script type="text/javascript">
jQuery(document).ready(function ($){
    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var ajaxaction = 'recipe_schema';
    $("#search_recipe_title").autocomplete({
        delay: 0,
        minLength: 2,
        source: function(req, response){
            $.getJSON(ajaxurl+'?callback=?&action='+ajaxaction, req, response);
        },
        select: function(event, ui) {
            ROBOAddRecipe(ui.item);
            return false;
            },
    });

    var $element = $('.awesome-recipe-list'),
          frame;


    $element.on( 'click', '.change-recipe-image', function( e ) {
      var ajax_nonce = '<?php echo wp_create_nonce( "update-post_" ); ?>';

      event.preventDefault();
      var elem, evt = e ? e:event;
      if (evt.srcElement)  elem = evt.srcElement;
      else if (evt.target) elem = evt.target;

      row = $(elem).parents('tr');
      $row = $(row);
      recipe_id = $row.find("input[name='recipe_id[]']")[0].value;
<?php if ( function_exists( 'wp_enqueue_media' ) ) { ?>
      frame = ROBONewSelectRecipeThumbnail(frame, $row);

<?php } else { ?>
      ROBOSelectRecipeThumbnail('<?php echo $post->ID ?>', recipe_id);
  <?php } ?>
    });

  });
</script>
<table class="form-table">
<tbody>
<tr valign="top">
<th scope="row"><label for="search_recipe_title"><strong>Include a Recipe</strong></label></th>
<td><input name="search_recipe_title" type="text" id="search_recipe_title" value="" class="regular-text">
<span class="description">Recipe title</span></td>
</tr>
</tbody></table>
<?php $recipe_list->display(); ?>
<div class="description">
<br>
To add a new Recipe go <a href='/wp-admin/edit.php?post_type=<?php echo RECIPE_SCHEMA_POSTTYPE ?>'>here</a> </div>


<?php




  }
}
?>
