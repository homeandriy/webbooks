<?php
namespace Webbooks\Statistics;

/** Atomic counters with a short-lived deduplication record; no raw IP storage. */
final class Views {
	public static function install(): void {
		global $wpdb;
		if ( get_option( 'webbooks_views_schema' ) !== '1' ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$collation = $wpdb->get_charset_collate();
			dbDelta( "CREATE TABLE {$wpdb->prefix}webbooks_views (
				post_id bigint(20) unsigned NOT NULL,
				views bigint(20) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY  (post_id)
			) $collation;" );
			dbDelta( "CREATE TABLE {$wpdb->prefix}webbooks_view_visitors (
				visitor_hash char(64) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				seen_at bigint(20) unsigned NOT NULL,
				PRIMARY KEY  (visitor_hash,post_id),
				KEY seen_at (seen_at)
			) $collation;" );
			update_option( 'webbooks_views_schema', '1', false );
		}
		if ( ! wp_next_scheduled( 'webbooks_cleanup_views' ) ) {
			wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'webbooks_cleanup_views' );
		}
	}

	public static function isPublic( int $id ): bool {
		$post = get_post( $id );
		return $post instanceof \WP_Post && $post->post_type === 'post' && $post->post_status === 'publish' && $post->post_password === '';
	}

	public static function count( int $id ): int {
		global $wpdb;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT views FROM {$wpdb->prefix}webbooks_views WHERE post_id = %d", $id ) );
		return $value === null ? max( 0, (int) get_post_meta( $id, '_views_count', true ) ) : (int) $value;
	}

	public static function record( int $id, string $ip ): int {
		global $wpdb;
		if ( ! self::isPublic( $id ) ) {
			throw new \InvalidArgumentException( 'The post is not public.' );
		}
		$hash = hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
		$now = time();
		$cutoff = $now - HOUR_IN_SECONDS;
		// An atomic conditional UPDATE elects exactly one request per visitor/post/hour.
		$inserted = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->prefix}webbooks_view_visitors (visitor_hash, post_id, seen_at) VALUES (%s, %d, %d)", $hash, $id, $now ) );
		$updated = $inserted === 0 ? $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}webbooks_view_visitors SET seen_at = %d WHERE visitor_hash = %s AND post_id = %d AND seen_at <= %d", $now, $hash, $id, $cutoff ) ) : 0;
		if ( $inserted === false || $updated === false ) {
			throw new \RuntimeException( 'Cannot record a visit.' );
		}
		if ( $inserted === 1 || $updated === 1 ) {
			$legacy = max( 0, (int) get_post_meta( $id, '_views_count', true ) );
			$written = $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}webbooks_views (post_id, views) VALUES (%d, %d) ON DUPLICATE KEY UPDATE views = views + 1", $id, $legacy + 1 ) );
			if ( $written === false ) {
				throw new \RuntimeException( 'Cannot update the counter.' );
			}
		}
		return self::count( $id );
	}

	public static function cleanup(): void {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}webbooks_view_visitors WHERE seen_at < %d", time() - DAY_IN_SECONDS ) );
	}
}
