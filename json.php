<?php

require('config.php');
require('db.php');

$tag = DEFAULT_TAG;
if ($_REQUEST['tag'] != "")
  $tag = $_REQUEST['tag'];

$json = $db->getPictures($tag, "moderation = '1'");

header("Content-type: application/json");
echo json_encode($json);

?>