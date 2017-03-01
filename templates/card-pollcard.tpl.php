<?php
/**
 * @var \Crowd\PollCard $this BaseCard or any child Card Object that has no own template
 */
?>

<div class="crowd-card crowd-card__poll">
	
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
		
		<div class="card-poll__title"><?php echo $this->get_title(); ?></div>
		
		<ul class="card-poll__option-list">
		<?php
		$options = $this->get_options();
		foreach ($options as $option){
			$label = $option["label"];
			$counter = $option["counter"];
			?>
			<li class="card-poll__option">
				<label class="card-poll__option-label">
					<input type="radio" name="<?php echo \Crowd\PollCard::POST_SELECTED_OPTION; ?>" value="<?php echo $label; ?>" />
					<span class="card-poll__option-text"><?php echo $label; ?> (<?php echo $counter; ?>)</span>
				</label>
			</li>
			<?php
		}
		?>
		</ul>
		
		<br>
		
		<button class="card-poll__submit" type="submit"><?php echo $this->get_submit_button_label(); ?></button>
		
	</form>
</div>