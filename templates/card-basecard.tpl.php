<?php
/**
 * @var \Crowd\BaseCard $this BaseCard or any child Card Object that has no own template
 */
?>
<div class="crowd-card crowd-card__base">
	<p><?php
		printf(
			_x('No Template found for Card "%1$s" TemplateName "%2$s". Using Base Template...', "BaseCard Template", \Crowd\Plugin::DOMAIN),
			$this->getName(),
			$this->getTemplateName()
		);
	?></p>
</div>
