<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
   <title>Imagechan</title>
   <link>http://nea.homelinux.net/rssparser/imagechan.php</link>
    <description>Imagechan</description>

<?php
require_once "XML/RSS.php";

$feed = "http://feeds.feedburner.com/Imagechan-HomeOfSeriouslyFunnyImages?feed=xml";

$rss =& new XML_RSS($feed);
$rss->parse();
foreach ($rss->getItems() as $item) {
  if (!file_exists("/var/tmp/rss/imgchan_".md5($item['link']))) {
    copy($item['link'], "/var/tmp/rss/imgchan_".md5($item['link']));
  }

  $page = file_get_contents("/var/tmp/rss/imgchan_".md5($item['link']));

  preg_match('/<div id="FullScreen"><a href="([^"]*)"/',$page, $matches_image);
  $image = explode("/", $matches_image[1]);
  if(!file_exists("/srv/http/tmp/imagechan/".$image[7]))
    copy($matches_image[1], "/srv/http/tmp/imagechan/".$image[7]);

  echo "<item>\n";
  echo "  <title>$item[title]</title>\n";
  echo "  <content:encoded><![CDATA[<img src=\"http://caddie.tamperd.net/tmp/imagechan/".$image[7]."\">]]></content:encoded>\n";
  echo "</item>\n";

}

?>
  </channel>
</rss>
