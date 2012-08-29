<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
   <title>lolpics.se</title>
   <link>http://caddie.tamperd.net/rss/lolpics_se.php</link>
    <description>lolpics.se</description>

<?php
require_once "XML/RSS.php";

$feed = "http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=lolpics_se";

$rss =& new XML_RSS($feed);
$rss->parse();
foreach ($rss->getItems() as $item) {
  preg_match('/lolpics_se: (.*) http:\/\/(.*)$/', $item['title'], $matches);

  if (!file_exists("/var/tmp/rss/lolpics_".md5($matches[2]))) {
    copy("http://".$matches[2], "/var/tmp/rss/lolpics_".md5($matches[2]));
  }

  $page = file_get_contents("/var/tmp/rss/lolpics_".md5($matches[2]));

  preg_match('/<img src="([^"]*)" alt="([^"]*)" class="photo" \/>/', $page, $matches_image);

  echo "<item>\n";
  echo "  <title>$matches[1]</title>\n";
  echo "  <link>http://".$matches[2]."</link>\n";
  echo "  <content:encoded><![CDATA[<img src=\"http://lolpics.se/".$matches_image[1]."\">]]></content:encoded>\n";
  echo "</item>\n";

}

?>
  </channel>
</rss>
