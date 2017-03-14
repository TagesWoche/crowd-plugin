<?php

get_header( 'embed' );

if ( have_posts() ) :
	while ( have_posts() ) : the_post();

		global $post;
		$card = \Crowd\CardClasses::get_card_object($post);
		echo $card->render();
		
	endwhile;
else :
	get_template_part( 'embed', '404' );
endif;

get_footer( 'embed' );
