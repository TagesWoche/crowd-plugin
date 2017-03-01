<?php
/**
 * template for cards list of post
 * @var array $cards
 */

echo "<ul class='crowd__cards-list'>";

foreach ($cards as $card){
	/**
	 * @var \Crowd\BaseCard $card
	 */
	echo "<li class='crowd__cards-list-item'>";
	crowd_render_card($card->post->ID);
	echo "</li>";
}
echo "</ul>";