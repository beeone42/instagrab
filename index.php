<?php

$tag = $_REQUEST['tag'];
$tmpfname = 'cookie.txt';
$ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36';

$csrftoken = get_csrftoken($tag, $tmpfname, $ua);
//echo $csrftoken; //exit ;

$pics = Array();
$id = 1;
$turns = 0;
do
  {
    $medias = get_medias($tag, $tmpfname, $ua, $csrftoken, $id);
    $id = $medias['media']['page_info']['end_cursor'];
    foreach ($medias['media']['nodes'] as $img)
      {
	$pics[$img['id']] = $img['thumbnail_src'];
      }
    $turns++;

    if (0)
    echo "turn: {$turns}, count: ".count($pics).
      ", start: ".$medias['media']['page_info']['start_cursor'].
      ", end: ".$medias['media']['page_info']['end_cursor']."\n";

    sleep(1);
  }
while (($turns < 5) && ($medias['media']['page_info']['start_cursor'] != $medias['media']['page_info']['end_cursor']));
//print_r($pics);

foreach ($pics as $p)
  {
    echo "<img src='{$p}' width='128px' />";
  }


function get_csrftoken($tag, $tmpfname, $ua)
{
  $url = "https://www.instagram.com/explore/tags/{$tag}/";
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
  
  if ($status != 200)
    {
      die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }
  curl_close($curl);
  return ($csrftoken);
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
  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_REFERER, $url1);

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