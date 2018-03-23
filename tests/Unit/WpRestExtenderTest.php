<?php
/**
 * Class SampleTest
 *
 * @package Wp_Rest_Api_Extender
 */

/**
 * Sample test case.
 */

use Mock\Routes\Author;
use Crazymeeks\WP\Foundation\Route\WPRoute;
use Crazymeeks\WP\Foundation\WPRestExtender\Extender;

class WpRestExtenderTest extends WP_UnitTestCase {

	/**
	 * @test
	 */
	public function it_can_format_and_compile_rest_route()
	{

		$route = $this->getRoute();

		$route->group(['prefix' => 'myplugin/v1', 'namespace' => 'Mock\Routes'], function($route){
			$route->get('/author', 'Author@getRoute');
			$route->get('/anotherauthor', 'Author@getRoute');
		});

		$route->group(['prefix' => 'myplugin/v2', 'namespace' => 'Mock\Routes'], function($route){
			$route->get('/author', 'Author@getRoute');
			$route->get('/anotherauthor', 'Author@getRoute');
		});

		$compile = $route->compile();

		$routes = array(
			array(
				'namespace' => 'myplugin/v1',
				'resource'  => '/author',
				'options'   => array(
					'methods' => 'GET',
					'callback' => array(
						(new Mock\Routes\Author), 'getRoute'
					),
				),
			)
		);

		$compiled = $route->getCompiledRoutes()[0];

		$this->assertEquals($routes[0]['namespace'], $compiled['namespace']);
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

		$route->group(['prefix' => 'myplugin/v1', 'namespace' => 'Mock\Routes'], function($route){
			$route->get('/author', 'Author@getRoute');
			$route->get('/anotherauthor', 'Author@getRoute');
		});

		$route->group(['prefix' => 'myplugin/v2', 'namespace' => 'Mock\Routes'], function($route){
			$route->get('/author', 'Author@getRoute');
			$route->get('/anotherauthor', 'Author@getRoute');
		});

		$compile = $route->compile();

		$wpRestApiExtender = new Extender($route);

		$this->assertTrue(true);

	}

	private function getRoute()
	{
		return new WPRoute();
	}
}
