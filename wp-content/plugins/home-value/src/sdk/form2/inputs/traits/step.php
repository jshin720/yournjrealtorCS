<?php

namespace plainview\sdk_eightb_home_value\form2\inputs\traits;

/**
	@brief		Step attribute manipulation.
	@author		Edward Plainview <edward@plainview.se>
	@copyright	GPL v3
	@version	20130524
**/
trait step
{
	/**
		@brief		Sets the input's step attribute.
		@param		int		$step		The new step attribute.
		@return		this		Object chaining.
		@since		20130524
	**/
	public function step( $step )
	{
		$step = intval( $step );
		return $this->set_attribute( 'step', $step );
	}
}

