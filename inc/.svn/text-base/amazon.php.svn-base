<?php
//require_once 'AmazonECS.class.php';

class Amazon {

  var $cache_table   = 'amazon_link_cache';
  private $ecs;


  public function __construct( $ecs ) {
    $this->ecs = $ecs;
  }


  

  function cache_remove() {
    global $wpdb;


    if ( !get_option( 'amazon_cached' ) ) return False;
    update_option( 'amazon_cached', 0 );


    $cache_table = $wpdb->prefix . $this->cache_table;
    $sql = "DROP TABLE $cache_table;";
    $wpdb->query( $sql );
    return True;
  }

  function cache_empty() {
    global $wpdb;

    if ( !get_option( 'amazon_cached' ) ) return False;

    $cache_table = $wpdb->prefix . $this->cache_table;
    $sql = "TRUNCATE TABLE $cache_table;";
    $wpdb->query( $sql );
    return True;
  }

  function cache_flush() {
    global $wpdb;

    $cache_table = $wpdb->prefix . $this->cache_table;
    $sql = "DELETE FROM $cache_table WHERE updated < DATE_SUB(NOW(),INTERVAL 48 HOUR);";
    $wpdb->query( $sql );
  }

  function cached_query( $asin ) {
    global $wpdb;
    $cache_table = $wpdb->prefix . $this->cache_table;
    $equipment = explode( ',', $asin );
    $equipment = array_filter( $equipment );


    $objects = array();


    $result = NULL;
    if ( get_option( 'amazon_cached' ) ) {
      // Check if asin is already in the cache

      $sql = "SELECT * FROM $cache_table WHERE asin IN ('". implode( "','", $equipment ) ."') AND  updated >= DATE_SUB(NOW(),INTERVAL 48 HOUR)";

      $result = $wpdb->get_results( $sql, ARRAY_A );
      if ( $result !== NULL ) {
        foreach ( $result as $item ) {
          $key = array_search( $item['asin'], $equipment );
          array_splice( $equipment, $key, 1 );
          $objects[] = $item;
        }
      }
    }



    if ( count( $equipment ) ) {
      try {
        $response = $this->ecs->responseGroup( 'Images,ItemAttributes' )->optionalParameters( array( 'Condition' => 'New' ) )->lookup( implode( ',', $equipment ) );



        if ( isset( $response->Items->Item ) ) {

          $items = $response->Items->Item;
          if ( is_array( $items ) ) {
            foreach ( $items as $item ) {
              $object = array(
                "title" => $item->ItemAttributes->Title,
                "asin" => $item->ASIN,
                "image" => $item->MediumImage->URL,
                "url"   => $item->DetailPageURL,
                "cc" => 'com',
                'updated' => current_time( 'mysql' ) );

              if ( isset( $item->ItemAttributes->ListPrice->FormattedPrice ) )
                $object["price"] = $item->ItemAttributes->ListPrice->FormattedPrice;
              else
                $object["price"] = "";
              $objects[] = $object;
            }
          } else {
            $item = $items;
            $object = array(
              "title" => $item->ItemAttributes->Title,
              "asin" => $item->ASIN,
              "image" => $item->MediumImage->URL,
              "url"   => $item->DetailPageURL,
              "cc" => 'com',
              'updated' => current_time( 'mysql' ) );
            if ( isset( $item->ItemAttributes->ListPrice->FormattedPrice ) )
              $object["price"] = $item->ItemAttributes->ListPrice->FormattedPrice;
            else
              $object["price"] = "";
            $objects[] = $object;
          }

          if ( get_option( 'amazon_cached' ) ) {
            foreach ( $objects as $object ) {
              $sql = "DELETE FROM $cache_table WHERE asin LIKE '". $object['asin'] ."' AND cc LIKE 'com'";
              $wpdb->query( $sql );
              $wpdb->insert( $cache_table, $object );
            }
          }


        }
      }
      catch( SoapFault $ex ) {
        error_log( $ex->getMessage() );

      }


    }



    return $objects;

  }
}

?>
