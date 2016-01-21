<?php

require('../config.php');
require('../db.php');

$db->setModeration($_REQUEST['id'], $_REQUEST['mod']);

?>