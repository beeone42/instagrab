<?php

require('config.php');
require('db.php');

$tag = DEFAULT_TAG;
if ($_REQUEST['tag'] != "")
  $tag = $_REQUEST['tag'];

$o = 0;
if ($_REQUEST['offset'] != "")
  $o = intval($_REQUEST['offset']);

$l = "";
if ($_REQUEST['limit'] != "")
  $l = " LIMIT {$o},".intval($_REQUEST['limit']);

$json = $db->getPictures($tag, "moderation = '1'", $l);

header("Content-type: application/json");
echo json_encode($json);

?>