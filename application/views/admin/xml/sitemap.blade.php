<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?=base_url();?></loc>
        <priority>1.0</priority>
    </url>
    @foreach($pages as $page)
    <url>
        <loc><?=base_url($page->path)?></loc>
        <priority>0.5</priority>
    </url>
    @endforeach
</urlset>