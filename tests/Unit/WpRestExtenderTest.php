<?php
/**
 * Class SampleTest
 *
 * @package Wp_Rest_Api_Extender
 */

/**
 * Sample test case.
 */

use Mock\Route\Author;
use Crazymeeks\WP\Foundation\Route\WPRoute;
use Crazymeeks\WP\Foundation\WPRestExtender\Extender;

class WpRestExtenderTest extends WP_UnitTestCase {

	/**
	 * @test
	 */
	public function it_can_format_and_compile_rest_route()
	{

		$route = $this->getRoute();

		$route->group(['prefix' => 'myplugin/v1', 'namespace' => 'Mock\Route'], function($route){
			$route->group(['prefix' => 'users'], function($route){
				$route->group(['prefix' => 'nextlevel'], function($route){
					$route->get('/author', 'Author@getRoute');
					$route->get('/anotherauthor', 'Author@getRoute');
				});
			});

			$route->get('/author', 'Author@getRoute');
			$route->get('/anotherauthor', 'Author@getRoute');
		});
		
		$routes = array(
			array(
				'prefix' => 'myplugin/v1/users/nextlevel',
				'resource'  => '/author',
				'options'   => array(
					'methods' => 'GET',
					'callback' => array(
						(new Mock\Route\Author), 'getRoute'
					),
				),
			)
		);

		$compiled = $route->getCompiledRoutes()[0];
		
		$this->assertEquals($routes[0]['prefix'], $compiled['prefix']);
		$this->assertEquals($routes[0]['resource'], $compiled['resource']);
		$this->assertEquals($routes[0]['options']['methods'], $compiled['options']['methods']);
		$this->assertInstanceOf(Author::class, $compiled['options']['callback'][0]);
	}

	/**
	 * @test
	 */
	public function it_can_register_rest_routes()
	{

		$route = $this->getRoute();

		$route->group(['prefix' => 'myplugin/v1', 'namespace' => 'Mock\Route'], function($route){
			$route->get('/author', 'Author@getRoute');
			$route->get('/anotherauthor', 'Author@getRoute');
		});


		$wpRestApiExtender = new Extender($route);

		$this->assertTrue(true);

	}

	private function getRoute()
	{
		return new WPRoute();
	}
}


/*
foreach($routes as $route){
	register_rest_route($route['namespace'], $route['resource'], $route['options']);
}

register_rest_route( 'myplugin/v1', '/author/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'my_awesome_func',
		'args' => array(
			'id' => array(
				'validate_callback' => function($param, $request, $key) {
					return is_numeric( $param );
				}
			),
		),
	) );
 */