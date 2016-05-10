<?php

include_once('./admin/other/get.php');

date_default_timezone_set('UTC');

$baseXMLFeed = simplexml_load_file("./admin/other/base.xml") or die("Error: Cannot create object");

header("Content-type: text/xml");

$baseXMLFeed->channel->title = get('autocms-settings.json', 'site-name');
$baseXMLFeed->channel->language = get('autocms-settings.json', 'site-lang');
$baseXMLFeed->channel->description = get('autocms-settings.json', 'site-description');
$temp = $baseXMLFeed->channel->addChild('atom:link', '', 'http://www.w3.org/2005/Atom');
$temp->addAttribute('href', get('autocms-settings.json', 'site-host') . 'feed/');
$temp->addAttribute('rel', 'self');
$baseXMLFeed->channel->addChild('link', get('autocms-settings.json', 'site-host'));

$categories = explode(",", get('autocms-settings.json', 'site-categories'));
if (!empty($categories)) {
    foreach ($categories as $category) {
        $baseXMLFeed->channel->addChild('category', $category);
    }
}

$count = blogCount('autocms-settings.json', 'rss-count');
for($var = 0 ; $var < $count ; $var++) {
    $baseXMLFeed->channel->item[$var]->title = getBlog('title', $var);
    $baseXMLFeed->channel->item[$var]->link = get('autocms-settings.json', 'site-host') . getBlog('link-href', $var);
    $baseXMLFeed->channel->item[$var]->pubDate = date(DATE_RFC2822, getBlog('published', $var));
    $baseXMLFeed->channel->item[$var]->addChild('dc:creator', '<![CDATA[' . getBlog('author', $var) . ']]', 'http://purl.org/dc/elements/1.1/');
    $baseXMLFeed->channel->item[$var]->guid = getBlog('link-href', $var);
    $baseXMLFeed->channel->item[$var]->addChild('atom:summary', getBlog('description', $var), 'http://www.w3.org/2005/Atom');
    $baseXMLFeed->channel->item[$var]->description = '<![CDATA[' . getBlog('description', $var) . ']]';
    $baseXMLFeed->channel->item[$var]->addChild('content:encoded', '<![CDATA[' . getBlog('full-blog', $var) . ']]', 'http://purl.org/rss/1.0/modules/content/');
    //$baseXMLFeed->channel->item[$var]->enclosure = '<![CDATA[' . getBlog('description', $var) . ']]';

    $categories = explode(",", getBlog('categories', $var));
    foreach($categories as $category) {
        $baseXMLFeed->channel->item[$var]->addChild('category', $category);
    }
}

echo $baseXMLFeed->asXML();