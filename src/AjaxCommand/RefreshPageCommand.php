<?php

namespace Drupal\account_modal\AjaxCommand;

use Drupal\Core\Ajax\CommandInterface;

class RefreshPageCommand implements CommandInterface {
	/**
	* Implements Drupal\Core\Ajax\CommandInterface:render().
	*/
	public function render() {
		return [
			'command' => 'accountModalRefreshPage',
		];
	}
}
