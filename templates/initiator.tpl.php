<?php
/**
 * @var WP_User|null $initiator
 */


if(null != $initiator){
	echo "<p>Initiated from {$initiator->display_name}</p>";
} else {
	echo "<p>No initiator</p>";
}

?>
