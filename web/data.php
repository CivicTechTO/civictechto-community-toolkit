<?php
  /* This wuold try to solve the IE problem, by first accessing a website from the outsite and then printing it out here..

Rapi Castillo
*/
  $gsheet_id = getenv('TOOLKIT_GSHEET_ID');
  $url = "https://docs.google.com/spreadsheets/d/{$gsheet_id}/export?gid=0&format=csv";
  $content = file_get_contents($url);
//  $data = json_decode($content);

  echo $content;
?>
