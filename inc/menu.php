<?php

namespace Crowd;


class Menu {
	
	const SLUG = "crowd";
	
	/**
	 * Menu constructor.
	 *
	 * @param \Crowd\Plugin $plugin
	 */
	function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}
	
	/**
	 * register admin menu page for settings
	 */
	function admin_menu(){
		add_menu_page(
			_x('Talk to Me', 'Page title' ,Plugin::DOMAIN),
			_x('Talk to Me', 'Menu title' ,Plugin::DOMAIN),
			'edit_posts',
			self::SLUG,
			null, // submenu pages will render output
			"data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMTAwIDEwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwIDEwMCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PHBhdGggZmlsbD0id2hpdGUiIGQ9Ik03MC42LDU2LjFoOC4ydjguMmgtOC4yVjU2LjF6IE05Ny44LDYwLjJjMCw3LTIuNSwxMy44LTcsMTkuNGMwLjgsMS42LDIsMi44LDMuNCwzLjdjMS41LDEsMi4yLDIuNywxLjgsNC40ICBjLTAuNCwxLjctMS43LDIuOS0zLjUsMy4yYy0xLjIsMC4yLTIuNCwwLjMtMy42LDAuM2MtMy42LDAtNi45LTAuNy05LjktMi4yYy01LjYsMi45LTEyLDQuNC0xOC42LDQuNGMtMjAuNiwwLTM3LjQtMTQuOS0zNy40LTMzLjIgIFMzOS43LDI3LDYwLjMsMjdTOTcuOCw0MS45LDk3LjgsNjAuMnogTTkxLjMsNjAuMmMwLTE0LjgtMTMuOS0yNi44LTMxLTI2LjhjLTE3LjEsMC0zMSwxMi0zMSwyNi44YzAsMTQuOCwxMy45LDI2LjgsMzEsMjYuOCAgYzYuMSwwLDExLjktMS41LDE3LTQuNGwxLjYtMC45bDEuNiwxYzEuNywxLDMuNiwxLjcsNS43LDJjLTEtMS4zLTEuNy0yLjgtMi4zLTQuNGwtMC43LTEuOGwxLjMtMS40Qzg5LDcyLjEsOTEuMyw2Ni4zLDkxLjMsNjAuMnogICBNNTYuMyw2NC4zaDguMnYtOC4yaC04LjJWNjQuM3ogTTQyLDY0LjNoOC4ydi04LjJINDJWNjQuM3ogTTQuMiw1Ny41Yy0wLjcsMC40LTAuNSwxLjUsMC4zLDEuNmMyLjgsMC40LDcuNSwwLjUsMTEuOS0yLjIgIGMwLDAsMCwwLDAsMGMxLjktMjAuMywyMC45LTM2LjMsNDQuMS0zNi4zYzAsMCwwLDAsMCwwYy01LjMtOC4zLTE1LjUtMTQtMjcuMi0xNEMxNi4yLDYuNiwyLjIsMTguOCwyLjIsMzMuOSAgYzAsNi41LDIuNiwxMi41LDYuOSwxNy4yQzguNCw1My4zLDYuOSw1NS43LDQuMiw1Ny41eiI+PC9wYXRoPjwvc3ZnPg==",
			25
		);
	}
}