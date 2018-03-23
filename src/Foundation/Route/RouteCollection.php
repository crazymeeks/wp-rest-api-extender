<?php

namespace Crazymeeks\WP\Foundation\Route;

use Crazymeeks\WP\Foundation\Route\Route;

class RouteCollection
{
	
	/**
	 * The collection of all routes
	 * 
	 * @var array
	 */
	protected $collections = [];


	/**
	 * Add route to collections
	 * 
	 * @param  Crazymeeks\WP\Foundation\Route\Route $route
	 */
	public function add(Route $route)
	{
		$this->collections = $route->getRoutes();
	}

	/**
	 * Get collections of routes
	 * 
	 * @return array
	 */
	public function all()
	{
		return $this->collections;
	}

}