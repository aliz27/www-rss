<?php

require_once 'youtube/youtube.php';
require_once 'youtube/curl.php';

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_YouTube');

$youtube = new youtube();
$youtubeUser = $_GET['username'];

$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
            <rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\">
                <channel>
                    <title>".$youtubeUser." on YouTube</title>
                    <link>http://caddie.tamperd.net/rss/youtube.php</link>
            ";

switch ($youtubeUser) {
  case 'simonscat':
  case 'simonscatextra':
  case 'corridordigital':
  case 'destinws2':
  case 'collegehumor':
  case 'knowyourmeme':
  case 'samandniko':
  case 'minutephysics':
  case 'DiscoveryNetworks':
  case 'freddiew':
  case 'freddiew2':
    $yt = new Zend_Gdata_YouTube();
    $yt->setMajorProtocolVersion(2);
    $videoFeed = $yt->getUserUploads($youtubeUser);
    foreach ($videoFeed as $videoEntry) {
      $urls = $youtube->get($videoEntry->getVideoWatchPageUrl());
      if ($urls[0]["ext"] == "mp4" ) {
        $date = date("D, d M Y H:i:s O", strtotime($videoEntry->mediaGroup->uploaded));
        $output .= "<item>
		    <title>".htmlspecialchars($videoEntry->getVideoTitle())."</title>
                    <link>".$urls[0]["url"]."</link>
		    <enclosure url=\"".htmlspecialchars($urls[0]["url"])."\" length=\"".htmlspecialchars($urls[0]["size"])."\" type=\"video/mpeg\"/>
		    <pubDate>".htmlspecialchars($date)."</pubDate>
                    <description>".htmlspecialchars(strip_tags($videoEntry->getVideoDescription()))."</description>
                </item>
		";
	}
    } 
    break;
}

$output .= "</channel>
	</rss>";
//header("Content-Type: application/rss+xml");
echo $output;

?>
