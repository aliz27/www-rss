<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
   <title>Comics</title>
   <link>http://caddie.tamperd.net/rss/comic.php</link>
    <description>Webcomics</description>

<?php
require_once "XML/RSS.php";

function cache($url) {
  if (!file_exists("/var/tmp/rss/comic_".md5($url))) {
    copy($url, "/var/tmp/rss/comic_".md5($url));
  }
  return file_get_contents("/var/tmp/rss/comic_".md5($url));
}

function printItem($title,$image) {
  echo "<item>\n";
  echo "  <title>$title</title>\n";
  echo "  <content:encoded><![CDATA[<img src=\"$image\">]]></content:encoded>\n";
  echo "</item>\n";
}

function parseUclick($feed) {
  $rss =& new XML_RSS($feed);
  $rss->parse();
  foreach ($rss->getItems() as $item) {
    $comic = cache($item['link']);
    preg_match('/<link rel="image_src" href="([^"]*)" \/>/', $comic, $matches_image);
    preg_match('/([^\n]*)/', $item['description'], $matches_title);
    printItem($matches_title[1],$matches_image[1]);
  }
}

function parseGoComic($feed, $search) {
  $rss =& new XML_RSS($feed);
  $rss->parse();
  foreach ($rss->getItems() as $item) {
    $comic = cache($item['link']);
    preg_match('/<img alt="'.$search.'" class="strip" onload="[^"]*" src="([^"]*)" width="600" \/>/', $comic, $matches_image);
    preg_match('/([^\n]*)/', $item['description'], $matches_title);
    printItem($matches_title[1],$matches_image[1]);
  }
}


$comic = $_GET['comic'];

switch ($comic) {
  case 'calvinandhobbes':
    $goFeed = "http://feeds.feedburner.com/uclick/calvinandhobbes?format=xml";
    $search = "Calvin and Hobbes";
    break;
  case 'garfield':
    $goFeed = "http://feeds.feedburner.com/uclick/garfield?format=xml";
    $search = "Garfield";
    break;
  case 'foxtrot':
    $goFeed = "http://feeds.feedburner.com/uclick/foxtrot?format=xml";
    $search = "FoxTrot";
    break;
  case 'foxtrotclassics':
    $goFeed = "http://feeds.feedburner.com/uclick/foxtrotclassics?format=xml";
    $search = "FoxTrot Classics";
    break;
  case 'shermanslagoon':
    if (!file_exists("/var/tmp/SL/SL".date("ymd").".gif")) {
      copy("http://www.slagoon.com/dailies/SL".date("ymd").".gif", "/var/tmp/SL/SL".date("ymd").".gif");
    }

    $SL = scandir("/var/tmp/SL/");
    foreach ($SL as $file) {
      if(is_file("/var/tmp/SL/".$file)) {
        printItem("Shermans lagoon ".date("Y-m-d", filemtime("/var/tmp/SL/".$file)), "http://www.slagoon.com/dailies/$file");
      }
    }
    break;
    case 'pennyarcade':
      $feed = "http://feeds.penny-arcade.com/pa-mainsite?format=xml";

      if ($feed) {
        $rss =& new XML_RSS($feed);
        $rss->parse();

        foreach ($rss->getItems() as $item) {
          if (preg_match('/Comic:/', $item['title'])) {
            $page = cache($item['link']);

            preg_match('/<img src="([^"]*)" alt="([^"]*)"/',$page, $matches_image);
            printItem("Penny Arcade ".date("Y-m-d", strtotime($item['pubdate'])), $matches_image[1]);
          }
        }
      }
      break;
  case 'cyanidandhappiness':
    $feed = "http://feeds.feedburner.com/Explosm";

    if ($feed) {
      $rss =& new XML_RSS($feed);
      $rss->parse();

      foreach ($rss->getItems() as $item) {
        if (preg_match('/New Cyanide and Happiness Comic./', $item['description'])) {
          $page = cache($item['link']);
          preg_match('/img alt="Cyanide and Happiness, a daily webcomic" src="([^"]*)"/', $page, $matches_image);

          if (
  	    $matches_image[1] == "http://explosm.net/db/files/Comics/placeholdah2.gif" || 
	    $matches_image[1] == "http://www.explosm.net/db/files/Comics/placeholdah2.gif" || 
	    $matches_image[1] == "http://www.explosm.net/db/files/Comics/placeholder.gif" ||
	    $matches_image[1] == "http://explosm.net/db/files/placeholder.gif" ||
	    $matches_image[1] == "http://explosm.net/db/files/Comics/placeholder.gif"
	  ) { } elseif ($matches_image[1]) {
            printItem("Cyanide &amp; Happiness ".date("Y-m-d", strtotime($item['pubdate'])) ,$matches_image[1]);
          }
        }
      }
    }
    break;
}

if (isset($ucFeed)) {
  parseUclick($ucFeed);
}
if (isset($goFeed)) {
  parseGoComic($goFeed, $search);
}

?>
  </channel>
</rss>
