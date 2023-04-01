<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
    <channel>
        <title><?php echo $feed_name; ?></title>
        <atom:link href="<?php echo $feed_url; ?>" rel="self" type="application/rss+xml" />
        <link><?php echo $feed_url; ?></link>
        <description><?php echo $page_description; ?></description>
        <lastBuildDate><?php echo date('now'); ?></lastBuildDate>
        <language><?php echo $site_language; ?></language>
        <sy:updatePeriod>
            hourly </sy:updatePeriod>
        <sy:updateFrequency>
            1 </sy:updateFrequency>
        <generator>https://github.com/gervisbermudez/startCodeIgniter-CSM</generator>
        <?php if($posts && count($posts)): ?>
            <?php foreach($posts as $post): ?>
                <item>
                    <title><?php echo xml_convert($post->title); ?></title>
                    <link><?php echo site_url($post->path) ?></link>
                    <comments><?php echo site_url($post->path) ?>#respond</comments>
                    <dc:creator>
                        <![CDATA[<?php echo ($post->user->username) ?>]]>
                    </dc:creator>
                    <pubDate><?php echo ($post->date_create) ?></pubDate>
                    <guid isPermaLink="false"><?php echo site_url($post->path) ?></guid>
                    <description>
                        <![CDATA[<?php echo character_limiter(strip_tags($post->content), 200); ?>]]>
                    </description>
                    <content:encoded>
                        <![CDATA[<?php echo character_limiter(strip_tags($post->content), 200); ?> ]]>
                    </content:encoded>
                    <wfw:commentRss><?php echo site_url($post->path) ?>/feed/</wfw:commentRss>
                    <slash:comments>0</slash:comments>
                </item>
            <?php endforeach; ?>
        <?php endif; ?>
    </channel>
</rss>