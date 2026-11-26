<?php
/** Two simultaneous PHP processes share an atomic test bucket; only one may create. */

$manifest = $write( 'parallel-image.json', json_encode( array( $valid->path, $valid->sha256, $valid->mime_type, $valid->extension, $valid->bytes, $valid->width, $valid->height ) ) );
$state_path = $write( 'parallel-bucket.json', json_encode( array( 'objects' => array(), 'requests' => array() ) ) );
$processes = array();
for ( $i = 0; $i < 2; ++$i ) {
	$pipes = array();
	$process = proc_open( array( PHP_BINARY, __DIR__ . '/r2-concurrency-worker.php', $manifest, $state_path ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
	if ( ! is_resource( $process ) ) { throw new RuntimeException( 'Cannot launch isolated concurrency worker' ); }
	fclose( $pipes[0] ); $processes[] = array( $process, $pipes );
}
$results = array(); $failures = array();
foreach ( $processes as [ $process, $pipes ] ) {
	$output = stream_get_contents( $pipes[1] ); $error = stream_get_contents( $pipes[2] );
	fclose( $pipes[1] ); fclose( $pipes[2] );
	$exit = proc_close( $process );
	if ( $exit !== 0 ) { $failures[] = 'Worker failed: ' . substr( $error, 0, 200 ); }
	else { $results[] = json_decode( $output, true, 512, JSON_THROW_ON_ERROR ); }
}
r2_test_assert( array() === $failures, implode( '; ', $failures ) ?: 'Both simultaneous SDK processes completed' );
$created = array_column( $results, 'created' ); sort( $created );
$bucket = json_decode( file_get_contents( $state_path ), true, 512, JSON_THROW_ON_ERROR );
r2_test_assert( $created === array( false, true ) && count( $bucket['objects'] ) === 1, 'Concurrent identical uploads create exactly one object and return one new/one existing result' );
r2_test_assert( $results[0]['url'] === $results[1]['url'], 'Concurrent callers receive the same content-addressed URL' );
$object = reset( $bucket['objects'] );
r2_test_assert( $object['actual_sha256'] === $valid->sha256, 'The concurrent upload stores exactly the validated original bytes' );
