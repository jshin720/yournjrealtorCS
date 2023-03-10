<?php

namespace plainview\sdk_eightb_home_value\form2\inputs\traits;

/**
	@brief		Manipulation of labels.
	@details

	Contains several methods to set the label. Directly, filtered, translated, sprintf'd, etc.

	The translation is handled by the specific SDK that inherits the form2 classes.

	@author		Edward Plainview <edward@plainview.se>
	@copyright	GPL v3
	@version	20130819
**/
trait label
{
	/**
		@brief		Retrieves the current label.
		@return		string		The current label.
		@since		20130524
	**/
	public function get_label()
	{
		return $this->label;
	}

	/**
		@brief		Sets the label.
		@details	In the name of consistence setting the label should be using set_label() instead of this method, but label() is really, really shorthand.
		@param		string		$label
		@return		this		Object chaining.
		@since		20130524
	**/
	public function label( $label )
	{
		return $this->set_label( $label );
	}

	/**
		@brief		Convenience function to first sprintf the label and then set it.
		@details	Uses the same parameters as sprintf().
		@param		string		$label
		@return		this		Object chaining.
		@since		20130524
	**/
	public function labelf( $label )
	{
		$labelf = @call_user_func_array( 'sprintf' , func_get_args() );
		if ( $labelf != '' )
			$label = $labelf;
		return $this->set_label( $label );
	}

	/**
		@brief		Convenience method function to first translate and then set the label.
		@param		string		$label		Label to translate and then set.
		@return		this		Object chaining.
		@since		20130524
	**/
	public function label_( $label )
	{
		$label = call_user_func_array( array( $this->container->form(), '_' ), func_get_args() );
		return $this->set_label( $label );
	}

	/**
		@brief		Filter and set the label.
		@param		string		$label		Label to filter and set.
		@return		this		Object chaining.
		@since		20130524
	**/
	public function set_label( $label )
	{
		return $this->set_unfiltered_label( \plainview\sdk_eightb_home_value\form2\form::filter_text( $label ) );
	}

	/**
		@brief		Set the label completely unfiltered.
		@param		string		$label		Label to be directly set.
		@return		this		Object chaining.
		@since		20130524
	**/
	public function set_unfiltered_label( $label )
	{
		$this->label->content = $label;
		return $this;
	}

	/**
		@brief		Convenience method to translate and then set the label directly. Does not filter the label.
		@param		string		$label		Label to translate and set.
		@return		this		Object chaining.
		@since		20130524
	**/
	public function set_unfiltered_label_( $label )
	{
		$label = call_user_func_array( array( $this->container->form(), '_' ), func_get_args() );
		return $this->set_unfiltered_label( $label );
	}
}
