<?php

namespace Test\Router;

use PHPUnit\Framework\TestCase;

class TestRouter extends \Framework\Router
{
    protected $namespace = 'Test\Router\\';

    public function getRoutes()
    {
        return $this->routes;
    }

    public function match($match)
    {
        return parent::match($match);
    }

    public function handleUrl($url)
    {
        return parent::handleUrl($url);
    }
}

class TestUsersController extends \Framework\Controller
{
    public function indexAction()
    {
        print 'this is Sparta!';
    }
}

/**
 */
final class RouterTest extends TestCase
{
    protected $router;

    public function setUp()
    {
        $this->router=new TestRouter;
        $this->router->add('users', 'testUsers#index');
    }

    public function testAddRoute()
    {
        $this->assertEquals(
            $this->router->getRoutes()[0],
            ['route' => 'users', 'to' => 'testUsers#index']
        );
    }

    public function testHandleUrl()
    {
        $this->assertEquals(
            $this->router->handleUrl('')[0],
            ''
        );

        $this->assertEquals(
            $this->router->handleUrl('/users')[1],
            'users'
        );
    }

    public function testMatch()
    {
        $this->assertFalse($this->router->match('cheesecake'));
        $this->assertEquals(
            $this->router->match('users'),
            ['controller' => 'TestUsersController', 'action' => 'indexAction']
        );
    }

    public function testDispatch()
    {
        $this->expectOutputString('this is Sparta!');
        $this->router->dispatch('/users');
    }
    /**
     * @expectedException Exception
     */
    public function testNoRouteException()
    {
         // $this->expectException(ExpectedException :: class);
         $this->router->dispatch('/oranges');
    }
}
