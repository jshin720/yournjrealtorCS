<?php

namespace eightb\home_value\actions;

class action
	extends \plainview\sdk_eightb_home_value\wordpress\actions\action
{
	public function get_prefix()
	{
		return 'eightb_home_value_';
	}
}
