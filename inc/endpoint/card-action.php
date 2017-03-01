<?php

namespace Crowd;

/**
 * Class CardAction
 * throw the action handling back to the card
 * @package Crowd
 */
class CardAction extends AbstractAction {
	
	/**
	 * value for Endpoint::VAR_ACTION
	 */
	const ACTION = "card_action";
	/**
	 * post id of card
	 */
	const CARD_ID = "card_pid";
	
	/**
	 * url of page to redirect to.
	 */
	const VAR_REDIRECT = "redirect_url";
	
	/**
	 * @return string
	 */
	function getActionSlug(){
		return self::ACTION;
	}
	
	/**
	 * handle request
	 */
	function handle($json) {
		
		/**
		 * @var BaseCard $card
		 */
		$card = CardClasses::get_card_object(get_post($json->{self::CARD_ID}));
		return $card->card_action($json);
	}
	
	/**
	 * @param \Crowd\BaseCard $card
	 */
	static function render_inputs(BaseCard $card){
		global $wp;
		?>
		<input
			type="hidden"
			name="<?php echo \Crowd\Endpoint::VAR_ACTION ?>"
			value="<?php echo \Crowd\CardAction::ACTION ?>"
		/>
		<?php // url to redirect to. if empty use json for ajax ?>
		<input
			type="hidden"
			name="<?php echo \Crowd\CardAction::VAR_REDIRECT ?>"
			value="<?php echo home_url(add_query_arg(array(),$wp->request)); ?>"
		/>
		
		<input
			type="hidden"
			name="<?php echo \Crowd\CardAction::CARD_ID ?>"
			value="<?php echo $card->get_ID(); ?>"
		/>
		<?php
		$card->render_inputs();
	}
}