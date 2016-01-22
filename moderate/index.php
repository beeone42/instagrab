<html><head>
<style>
div {
 margin: 3px;
 display: inline;
 text-align: center;
   float: left;
 }

div.block {
display: block;
}

span {
cursor:pointer;
margin: 3px;
}

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
   $(function(){

       $("button").click(function () {
	   //alert($(this).attr("mod") + ' / ' + $(this).attr("id"));
	   $("#d" + $(this).attr("id")).css("background-color", $(this).attr("mod") == 1 ? "lime" : "red");
	   $.get("ajax.php?id="+$(this).attr("id")+"&mod="+ $(this).attr("mod"), function(){ });
	 });

     });
</script>
</head>
<body>
<?php

require('../config.php');
require('../db.php');

$tag = DEFAULT_TAG;
if ($_REQUEST['tag'] != "")
  $tag = $_REQUEST['tag'];

$todo = $db->getPictures($tag, "moderation = '0'");
show_pics($todo);

echo "<div style='display: block; background-color: lime'>\n";
$good = $db->getPictures($tag, "moderation = '1'");
show_pics($good);
echo "</div>\n";

if ($_REQUEST['showbad'] == '1')
  {
    echo "<div class='block' style='background-color: red'>\n";
    $bad = $db->getPictures($tag, "moderation = '2'");
    show_pics($bad);
    echo "</div>\n";
    echo "<div class='block'><a href='index.php?tag={$tag}&showbad=0'>Hide bad pics...</a></div>";
  }
else
  {
    echo "<div class='block'><a href='index.php?tag={$tag}&showbad=1'>Show bad pics...</a></div>";
  }

function show_pics($pics)
{
  foreach ($pics as $p)
    {
      echo "<div id='d{$p[id]}'>";
      echo "<a href='https://www.instagram.com/p/{$p[code]}/' target='_blank'>";
      echo "<img id='ig{$p[id]}' src='{$p[thumb]}' width='128px' height='128px' /></a><br />";
      echo "<button mod='1' id='{$p[id]}'>YES</button> - <button mod='2' id='{$p[id]}'>NO</button>";
      echo "</div>";
    }
}

?>
</body></html>