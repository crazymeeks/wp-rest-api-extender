<?php

abstract class Facade
{

	protected static $class = [];
	
	public static function getFacadeAccessor()
	{
		die('The facade does not implement getFacadeAccessor() method');
	}


	protected static function getClass()
	{
		return self::resolveClass();
	}

	/**
	 * Resolve facade class
	 * 
	 * @return object
	 */
	protected static function resolveClass()
	{
		$facadeAccessor = static::getFacadeAccessor();

		return isset(self::$class[$facadeAccessor]) ? self::$class[$facadeAccessor] : self::$class[$facadeAccessor] = new $facadeAccessor; 
	}

	/**
	 * Handles static calls dynamically
	 *
	 * @param  string $name
	 * @param  mixed $args
	 * 
	 * @return mixed
	 */
	public static function __callStatic($name, $args)
	{
		$class = self::getClass();

		return $class->{$name}(...$args);
	}
}