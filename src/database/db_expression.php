<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       http://kaliphp.com
 */

namespace kaliphp\database;

class db_expression
{
	// Raw expression string
	protected $_value;

	/**
	 * Sets the expression string.
	 *
	 *     $expression = new db_expression('COUNT(users.id)');
	 *
	 * @param string $value  expression string
	 */
	public function __construct($value)
	{
		// Set the expression string
		$this->_value = $value;
	}

	/**
	 * Get the expression value as a string.
	 *
	 *     $sql = $expression->value();
	 *
	 * @return  string
	 */
	public function value()
	{
		return (string) $this->_value;
	}

	/**
	 * Return the value of the expression as a string.
	 *
	 *     echo $expression;
	 *
	 * @return  string
	 * @uses    db_expression::value
	 */
	public function __toString()
	{
		return $this->value();
	}
}
