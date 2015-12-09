<?php

include_once('./admin/other/get.php');

date_default_timezone_set('UTC');

$baseXMLFeed = simplexml_load_file("./admin/other/base.xml") or die("Error: Cannot create object");

header("Content-type: text/xml");

$baseXMLFeed->channel->title = get('autocms-settings.json', 'site-name');
$baseXMLFeed->channel->language = get('autocms-settings.json', 'site-lang');
$baseXMLFeed->channel->description = get('autocms-settings.json', 'site-description');
//$baseXMLFeed->channel->children('atom', true)->link['href'] = get('autocms-settings.json', 'site-host') . 'feed/';
$baseXMLFeed->channel->link = get('autocms-settings.json', 'site-host');

$count = blogCount('autocms-settings.json', 'rss-count');
for($var = 0 ; $var < $count ; $var++) {
    $baseXMLFeed->channel->item[$var]->title = getBlog('title', $var);
    $baseXMLFeed->channel->item[$var]->link = getBlog('link-href', $var);
    //$baseXMLFeed->channel->item[$var]->pubDate = getBlog('published', $var);
}

echo $baseXMLFeed->asXML();