<?php

namespace Framework;

/**
 * Router
 */
class Router
{
    /**
     * Namespace path
     *
     * @var string
     */
    protected $namespace = 'App\Controllers\\';

    /**
     * Routes array
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Adds route to array
     *
     * @param string $route  route
     * @param string $params should address controller and action ex: controller#action
     */
    public function add($route, $params)
    {
        $this->routes[]=[
          'route'=>$route,
          'to'=>$params,
        ];
    }

    /**
     * Dispatches the route by matching and launching controller and action (method)
     *
     * @param string $url route url
     */
    public function dispatch($url) 
    {
        $urlParts = $this->handleUrl($url);
        $match = $urlParts[1];
        $param = isset($urlParts[2]) ? $urlParts[2] : null;
        $matchResult = $this->match($match);
        if ($matchResult) {
            $controllerName = $this->namespace.$matchResult['controller'];
            if (class_exists($controllerName)) {   
                $controller = new $controllerName;
                $action = $matchResult['action'];
                if (is_callable([$controller, $action])) {
                    $controller->$action($param);
                } else {
                    throw new \Exception("Method $action (in controller $controller) not found");
                };
            } else {
                throw new \Exception('Controller class '.$controllerName.' not found');
            }
        } else {
            throw new \Exception('No route matched.', 404);
        }
    }

    /**
    * Matches the route and returns controller, action array ex ['controller'=>SomeController, 'action'=>someAction]
    *
    * @param  string $match 
    * @return array|url
    */
    protected function match($match)
    {
        foreach ($this->routes as $item) {
            if ($item['route'] === $match) {
                $data = explode('#', $item['to']);
                return [
                  'controller' => ucfirst($data[0].'Controller'),
                  'action' => $data[1].'Action',
                ];
            }
        }
        return false;
    }

    /**
    * Strips "/" from url, and converts to array where second element is url and thid oprional parameter (id)
    *
    * @param  string $url route url
    * @return array
    */
    protected function handleUrl($url)
    {
        return explode('/', $url);
    }
}

