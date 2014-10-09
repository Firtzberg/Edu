<?php

class BaseController extends Controller {

	const DANGER_MESSAGE_KEY = 'greska';
	const SUCCESS_MESSAGE_KEY = 'poruka';
	const START_HOUR = 8;
	const END_HOUR = 22;
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
