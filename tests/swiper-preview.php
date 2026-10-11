<?php
/** Disposable multi-image fixture, exclusively in the isolated preview database. */
require '/var/www/html/wp-load.php';
if ( DB_NAME !== 'webbooks_tests' ) { exit( 1 ); }
$id = wp_insert_post( array( 'post_title' => 'Swiper gallery preview', 'post_content' => '<p>Three images for navigation, thumbnails, zoom and dialog checks.</p>', 'post_status' => 'publish' ) );
update_post_meta( $id, 'gallery', array(
  array( 'url' => 'http://localhost:8896/wp-content/themes/webbooks/screenshot.png', 'alt' => 'Theme screenshot' ),
  array( 'url' => 'http://localhost:8896/wp-content/themes/webbooks/assets/images/book-placeholder.svg', 'alt' => 'Book cover' ),
  array( 'url' => 'http://localhost:8896/wp-content/themes/webbooks/assets/images/404-books.svg', 'alt' => 'Book illustration' ),
) );
echo 'Swiper preview post: ' . $id . "\n";
