<?php
namespace Shared\Http;

class Router {
    private $routes = [];
    private $request;
    private $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Load routes từ file
     */
    public function loadRoutes($routesFile) {
        $routes = require $routesFile;
        foreach ($routes as $route) {
            $this->addRoute($route[0], $route[1], $route[2], $route[3]);
        }
        return $this;
    }

    /**
     * Thêm một route
     */
    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
        ];
        return $this;
    }

    /**
     * Dispatch request đến controller phù hợp
     */
    public function dispatch() {
        $requestMethod = $this->request->getMethod();
        $requestPath = $this->request->getPath();

        foreach ($this->routes as $route) {
            $match = $this->matchRoute($route, $requestMethod, $requestPath);
            if ($match !== false) {
                return $this->handleRoute($route, $match);
            }
        }

        return $this->response->notFound('Endpoint not found');
    }

    // ============ Private Methods ============

    private function matchRoute($route, $method, $path) {
        if ($route['method'] !== $method) {
            return false;
        }

        return $this->matchPath($route['path'], $path);
    }

    private function matchPath($pattern, $path) {
        // Chuyển /api/v1/guests/:id thành regex
        $pattern = preg_replace('/:(\w+)/', '(?P<$1>[^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $path, $matches)) {
            return $matches;
        }
        return false;
    }

    private function handleRoute($route, $matches) {
        $controllerName = $route['controller'];
        $action = $route['action'];

        // Tạo instance của controller
        $controllerClass = "Api\\Controllers\\$controllerName";
        
        if (!class_exists($controllerClass)) {
            return $this->response->error("Controller $controllerName not found", 500);
        }

        try {
            $controller = new $controllerClass($this->request, $this->response);

            if (!method_exists($controller, $action)) {
                return $this->response->error("Action $action not found", 500);
            }

            // Lưu URL parameters vào request
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $this->request->setParam($key, $value);
                }
            }

            return call_user_func([$controller, $action]);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
