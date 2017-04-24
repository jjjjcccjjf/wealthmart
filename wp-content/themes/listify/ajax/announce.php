<?php

$path = $_SERVER['DOCUMENT_ROOT'] . '/wealthmart'; #TODO: Change me on live

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

$ids = explode(',', $_POST['ids']);
$announcement = $_POST['announcement'];

foreach ($ids as $id) {
  $data['receiver_id'] = $id;
  $data['sender_id'] = $GLOBALS['current_user']->ID;
  $data['message'] = $announcement;
  $data['type'] = 2;
  $wpdb->insert('advisor_inbox', $data);
}
