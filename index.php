<?php

require("config.php");
require("db.php");

set_time_limit(600);

$tag = $_REQUEST['tag'];
$tmpfname = 'cookie.txt';
$ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36';

// {"has_previous_page":false,"start_cursor":"1162234081011539497","end_cursor":"1004470932121113243","has_next_page":true}
list($csrftoken, $id) = get_csrftoken($tag, $tmpfname, $ua);
//echo $csrftoken; //exit ;

/*
                    [0] => Array
		      (
                            [code] => BAhFb0oi54p
                            [date] => 1452769120
                            [dimensions] => Array
			    (
                                    [width] => 1080
                                    [height] => 1080
			     )

                            [comments] => Array
			    (
                                    [count] => 0
			     )

                            [caption] => In the world there are "10" types of people, those which include binary , and others.

#42born2code
                            [likes] => Array
			    (
                                    [count] => 11
			     )

                            [owner] => Array
			    (
                                    [id] => 605445742
			     )

                            [thumbnail_src] => https://scontent-cdg2-1.cdninstagram.com/hphotos-xtp1/l/t51.2885-15/s640x640/sh0.08/e35/12523695_549959975159923_1617068191_n.jpg
                            [is_video] => 
                            [id] => 1162234081011539497
                            [display_src] => https://scontent-cdg2-1.cdninstagram.com/hphotos-xtp1/l/t51.2885-15/e35/12523695_549959975159923_1617068191_n.jpg
		       )
		      */

$pics = Array();
$turns = 0;
do
  {
    $medias = get_medias($tag, $tmpfname, $ua, $csrftoken, $id);
    //print_r($medias);
    foreach ($medias['media']['nodes'] as $img)
      {
	$pics[$img['id']] = Array(
				  "id"		=> $img['id'],
				  "thumb"	=> $img['thumbnail_src'],
				  "full"	=> $img['display_src']
				  );
      }
    $turns++;

    if (1)
    echo "turn: {$turns}, count: ".count($pics).", id: {$id}".
      ", start: ".$medias['media']['page_info']['start_cursor'].
      ", end: ".$medias['media']['page_info']['end_cursor']."\n";

    $id = $medias['media']['page_info']['end_cursor'];
    sleep(0.5);
  }
while (($turns < 5) && ($medias['media']['page_info']['start_cursor'] != $medias['media']['page_info']['end_cursor']));
//print_r($pics);
//exit;

foreach ($pics as $p)
  {
    //echo "<a href=''><img src='{$p[thumb]}' alt='{$p[id]}' width='128px' /></a>";
    if (!$db->alreadyExistsPicture($p['id']))
      {
	$db->storePicture($p['id'], $tag, $p['thumb'], $p['full']);
	echo "<a href=''><img src='{$p[thumb]}' alt='{$tag} #{$p[id]}' width='128px' /></a>";
      }
  }


function get_csrftoken($tag, $tmpfname, $ua)
{
  $url = "https://www.instagram.com/explore/tags/{$tag}/";
  $id = 0;
  $csrftoken = "";
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_COOKIEJAR, $tmpfname);
  curl_setopt($curl, CURLOPT_COOKIEFILE, $tmpfname);
  curl_setopt($curl,CURLOPT_USERAGENT, $ua);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLINFO_HEADER_OUT, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, false);
  $response = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $headers = curl_getinfo ($curl, CURLINFO_HEADER_OUT );
  if (preg_match('/csrftoken=([a-f0-9]+)/', $headers, $regs))
    {
      $csrftoken = $regs[1];
    }
  if (preg_match('/\"start_cursor\":\"([0-9]+)\"/', $response, $regs))
    {
      print_r($regs);
      $id = $regs[1];
    }
  
  if ($status != 200)
    {
      die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }
  curl_close($curl);
  return (Array($csrftoken, $id));
}

function get_medias($tag, $tmpfname, $ua, $csrftoken, $id)
{
  $url = "https://www.instagram.com/query/";
  $content = "q=ig_hashtag({$tag})+%7B+media.after({$id}%2C+100)+%7B%0A++count%2C%0A++nodes+%7B%0A++++caption%2C%0A++++code%2C%0A++++comments+%7B%0A++++++count%0A++++%7D%2C%0A++++date%2C%0A++++dimensions+%7B%0A++++++height%2C%0A++++++width%0A++++%7D%2C%0A++++display_src%2C%0A++++id%2C%0A++++is_video%2C%0A++++likes+%7B%0A++++++count%0A++++%7D%2C%0A++++owner+%7B%0A++++++id%0A++++%7D%2C%0A++++thumbnail_src%0A++%7D%2C%0A++page_info%0A%7D%0A+%7D&ref=tags%3A%3Ashow";
  $curl = curl_init($url);

  curl_setopt($curl, CURLOPT_COOKIEJAR, $tmpfname);
  curl_setopt($curl, CURLOPT_COOKIEFILE, $tmpfname);
  curl_setopt($curl,CURLOPT_USERAGENT, $ua);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLINFO_HEADER_OUT, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER,
	      array("Accept: application/json, text/javascript, */*; q=0.01",
		    "Origin: https://www.instagram.com",
		    "Content-type: application/x-www-form-urlencoded; charset=UTF-8",
		    "X-CSRFToken: {$csrftoken}",
		    "X-Instagram-AJAX: 1",
		    "X-Requested-With: XMLHttpRequest",
		    "Referer: https://www.instagram.com/explore/tags/{$tag}/"
		    )
	      );
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
  $json_response = curl_exec($curl);
  
  //print_r(curl_getinfo ($curl, CURLINFO_HEADER_OUT ));

  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  
  if ( $status != 200 ) {
    die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
  }

  curl_close($curl);
  //echo $json_response;
  $response = json_decode($json_response, true);
  return ($response);
}

?>