<?php

$path = $_SERVER['DOCUMENT_ROOT'] . '/wealthmart'; #TODO: Change me on live

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

if(
  $wpdb->delete('advisor_details_meta', array('meta_id' => $_POST['meta_id']))
){
  echo 1;
}else{
  echo 0;
}
