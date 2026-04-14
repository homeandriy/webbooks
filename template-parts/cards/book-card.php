<?php
$thumb_url = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
?>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 content_block book-card-grid-item">
    <div class="list-group book-card" itemscope itemtype="http://schema.org/Book">
        <div class="list-group-item book-card-item"><div class="row book-card-row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 book-card-image-col">
                <div class="book-card-image">
                    <img width="390" height="440" class="media-object lazy" data-original="<?= esc_url($thumb_url) ?>" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mMMDk76DwAEEgIJ2SbKSQAAAABJRU5ErkJggg==" alt="<?= esc_attr(get_the_title()) ?>" itemprop="image">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 next-reed-column book-card-content-col">
                <h4 class="list-group-item-heading book-card-title"><a href="<?= esc_url(get_the_permalink()) ?>" itemprop="name"><?= esc_html(get_the_title()) ?></a></h4>
                <div class="next-reed-content">
                    <p class="list-group-item-text next-reed-description" itemprop="description"><?= wp_kses_post(get_short_description(get_the_content(), 50)) ?></p>
                    <table class="table book-card-meta"><tbody>
                    <tr><td><?php esc_html_e('Author:', 'webbooks'); ?></td><td itemprop="author"><?= esc_html(get_post_meta($post->ID, 'autor', true)); ?></td></tr>
                    <tr><td><?php esc_html_e('Publication year:', 'webbooks'); ?></td><td itemprop="copyrightYear"><?= esc_html(get_post_meta($post->ID, 'year', true)); ?></td></tr>
                    <tr><td><?php esc_html_e('Publisher:', 'webbooks'); ?></td><td><?= esc_html(get_post_meta($post->ID, 'create', true)); ?></td></tr>
                    <tr><td><?php esc_html_e('Language:', 'webbooks'); ?></td><td itemprop="inLanguage"><?= esc_html(\Webbooks\Book\BookMeta::getLanguage((string)get_field('language'))); ?></td></tr>
                    <tr><td><?php esc_html_e('Status:', 'webbooks'); ?></td><td><?= esc_html(\Webbooks\Book\BookMeta::getComplexity(trim((string)get_field('complexity')))); ?></td></tr>
                    <tr><td><?php esc_html_e('Format:', 'webbooks'); ?></td><td><?= esc_html(get_post_meta($post->ID, 'format', true)); ?></td></tr>
                    </tbody></table>
                </div>
                <div class="next-reed book-card-footer"><p><a href="<?= esc_url(get_the_permalink()) ?>" class="btn navbar-btn btn-info"><?php esc_html_e('More', 'webbooks'); ?></a></p></div>
            </div>
        </div></div>
    </div>
</div>
