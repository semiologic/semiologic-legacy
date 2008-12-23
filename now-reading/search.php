<?php

include sem_path . '/header.php';


do_action('before_the_entries');
do_action('before_the_entry');

?><div class="entry now-reading">

	<?php if( can_now_reading_admin() ) : ?>
		<p>Admin: &raquo; <a href="<?php manage_library_url() ?>">Manage Books</a></p>

	<?php endif; ?>
	<p><a href="<?php library_url() ?>">&larr; Back to library</a></p>

	<?php library_search_form() ?>
	<p>Search results for <?php search_query(); ?>:</p>

	<?php if( have_books("status=all&num=-1&search={$GLOBALS['query']}") ) : ?>
		<ul>

		<?php while( have_books("status=all&num=-1&search={$GLOBALS['query']}") ) : the_book(); ?>
			<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a> by <a href="<?php book_author_permalink() ?>"><?php book_author() ?></a></li>

		<?php endwhile; ?>
		</ul>

	<?php else : ?>
		<p>Sorry, but there were no search results for your query.</p>

	<?php endif; ?></div>
<?php

do_action('after_the_entry');
do_action('after_the_entries');

include sem_path . '/footer.php';
?>