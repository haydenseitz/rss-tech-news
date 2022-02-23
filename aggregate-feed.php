<?php 

// GIST from https://gist.github.com/smajda/204194

/* Merge multiple RSS feeds with SimplePie
 *
 * Just modify the path to SimplePie and 
 * modify the $feeds array with the feeds you want
 *
 * You should probably also change the channel title, link and description,
 * plus I added a CC license you may not want
 *
 * Help from: http://www.webmaster-source.com/2007/08/06/merging-rss-feeds-with-simplepie/ 
 *
*/
header('Content-Type: application/rss+xml; charset=UTF-8');

// Your path to simplepie
include_once('/path/to/simplepie/simplepie.inc'); // Include SimplePie

//$feedlink = "http://somedomain.com/thisfeed/"; // URL for this feed, <atom:link>
//$feedtitle = "Jon's Feeds"; // <title>
//$feedhome = "http://jon.smajda.com"; // <link>
//$feeddesc = "One Feed to Aggregate Them All"; // <link>

$feedlink = "http://somedomain.com/thisfeed/"; // URL for this feed, <atom:link>
$feedtitle = "Jon's Feeds"; // <title>
$feedhome = "http://jon.smajda.com"; // <link>
$feeddesc = "One Feed to Aggregate Them All"; // <link>

// Feeds you want to aggregate
$feeds = array(
    'http://jon.smajda.com/rss.xml',
    'http://smajda.tumblr.com/rss',
	'http://files.smajda.com/jon/feeds/greader/',
    'http://twitter.com/statuses/user_timeline/14285636.rss',
	'http://feeds.delicious.com/v2/rss/smajda',
    'http://contexts.org/howto/feed/',
    'http://github.com/smajda.atom'
);

echo '<?xml version="1.0" encoding="UTF-8"?>'; 
?>
<rss version="2.0" 
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:content="http://purl.org/rss/1.0/modules/content/" 
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:creativeCommons="http://backend.userland.com/creativeCommonsRssModule"
>
<channel>
<title><?php echo $feedtitle; ?></title>
<atom:link href="<?php echo $feedlink; ?>" rel="self" type="application/rss+xml" />
<link><?php echo $feedhome; ?></link>
<description><?php echo $feeddesc; ?></description>
<language>en-us</language>
<copyright>Copyright <?php echo '2007-'.date("Y");  ?></copyright>
<creativeCommons:license>http://creativecommons.org/licenses/by-nc-sa/3.0/</creativeCommons:license>

<?php
date_default_timezone_set('America/Chicago');
$feed = new SimplePie(); // Create a new instance of SimplePie
// Load the feeds
$feed->set_feed_url($feeds);
$feed->set_cache_duration (600); // Set the cache time
$feed->enable_xml_dump(isset($_GET['xmldump']) ? true : false);
$success = $feed->init(); // Initialize SimplePie
$feed->handle_content_type(); // Take care of the character encoding
?>


<?php if ($success) {
$itemlimit=0;
foreach($feed->get_items() as $item) {
if ($itemlimit==40) { break; }
?>

    <item>
        <title><?php echo $item->get_title(); ?></title>
        <link><?php echo $item->get_permalink(); ?></link>
        <guid><?php echo $item->get_permalink(); ?></guid>
        <pubDate><?php echo $item->get_date('D, d M Y H:i:s T'); ?></pubDate>
        <dc:creator><?php if ($author = $item->get_author()) { echo $author->get_name()." at "; }; ?><?php if ($feed_title = $item->get_feed()->get_title()) {echo $feed_title;}?></dc:creator>
        <description>
        <?php echo htmlspecialchars(strip_tags($item->get_description())); ?>
        </description>
        <content:encoded><![CDATA[<?php echo $item->get_content(); ?>]]></content:encoded>
		<creativeCommons:license>http://creativecommons.org/licenses/by-nc-sa/3.0/</creativeCommons:license>
    </item>
<?
$itemlimit = $itemlimit + 1;
}
}
?>
</channel>
</rss>
