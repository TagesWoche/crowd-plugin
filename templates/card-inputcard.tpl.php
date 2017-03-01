<?php
/**
 * @var \Crowd\InputCard $this BaseCard or any child Card Object that has no own template
 */
?>
<div class="crowd-card crowd-card__input">
	
	<?php
	
	if($this->hasSubmitSuccess()){
		?>
		<p>Thanks for your submisson.</p>
		<?php
	}
	
	?>
	
	<form action="/<?php echo \Crowd\Endpoint::URL; ?>" method="post">
		
		<?php
		// handle form submit with $this->card_action($data)
		\Crowd\CardAction::render_inputs($this);
		?>
		
		<label class="card-input__label">
			<span class="card-input__title"><?php echo $this->get_title(); ?></span>
			<br/>
			<textarea
					class="card-input__textarea"
					name="<?php echo \Crowd\InputCard::POST_INPUT_USER_CONTENT; ?>"
					placeholder="<?php echo $this->get_placeholder_text(); ?>"
			></textarea>
		</label>
		<br>
		<button class="card-input__submit" type="submit"><?php echo $this->get_submit_button_label(); ?></button>
	
	</form>
	
</div>

