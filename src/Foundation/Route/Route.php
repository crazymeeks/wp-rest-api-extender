<?php

namespace Crazymeeks\WP\Foundation\Route;

class Route
{
	
	protected $routes = [];

	public function __construct(array $routes)
	{
		$this->routes = $routes;
	}

	public function getRoutes()
	{
		return $this->routes;
	}
}