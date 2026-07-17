<?php
/**
 * JSON-LD script template.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args
 */

$graph = $args['graph'] ?? array();
?>
<script type="application/ld+json"><?php echo wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
