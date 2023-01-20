<?php

namespace plainview\sdk_eightb_home_value\wordpress\form2\inputs;

class secondary_button
	extends button
{
	public function _construct()
	{
		$this->css_class( 'button-secondary' );
	}
}
