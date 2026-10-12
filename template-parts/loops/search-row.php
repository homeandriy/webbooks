<?php
/**
 * Search result table row.
 *
 * @package WordPress
 * @subpackage webbooks
 */

?>
<tr class="webbooks-search-row">
	<td>
		<h5>
			<a href="<?php the_permalink(); ?>" class="card-title">
				<?php the_title(); ?>
			</a>
		</h5>
	</td>
</tr>
