<?php

namespace App\Helper;

use Illuminate\Support\Str;

/**
 * in the name of allah
 * Created by PhpStorm.
 * User: Parham
 * Date: 10/23/2017
 * Time: 5:15 PM
 */
class RouteConvertor
{
    private $excludeFunction = ['__construct', 'middleware', 'getMiddleware', 'callAction', '__call'];
    private $excludeMethods = ['getIndex', 'postIndex'];
    private $pathController = 'App\Http\Controllers';

    public function convertRouteController($routeController)
    {
        try {
            $infoController = $this->getInfoFromRoute($routeController);
            if ($infoController) {
                $methods = new \ReflectionClass($infoController['classFullPath']);
                $methods = $methods->getMethods();
                $result = '';
                if (is_array($methods)) {
                    $result .= "<br>";
                    $result .= "#################################### START SECTION" . $infoController['classPath'] . '####################################';
                    $result .= "<br>";
                    foreach ($methods as $method) {
                        if ($method->isPublic() && $method->isUserDefined()) {
                            if (!in_array($method->getName(), $this->excludeFunction)) {
                                $typeMethod = self::checkType($method->getName());
                                if ($typeMethod) {
                                    $result .= $this->BuildRoute($typeMethod, $infoController['url'], $method->getName(), $infoController['classPath'], $infoController['classFullPath']);
                                    $result .= "<br>";
                                }
                            }
                        }
                    }
                    $result .= "#################################### END SECTION" . $methods['classPath'] . '####################################';
                    $result .= "<br>";
                }
                return $result;
            }
        } catch (\Exception $e) {
            echo $routeController;
            dd($e);
            die();
        }
    }

    private function checkType($methodName)
    {
        if (Str::startsWith($methodName, 'get')) {
            return 'get';
        } else if (Str::startsWith($methodName, 'post')) {
            return 'post';
        }
        return false;
    }

    private function BuildRoute($type, $name, $function, $prefix, $fullPathClass)
    {
        $params = $this->buildRouteParams($fullPathClass, $function);
        if (in_array($function, $this->excludeMethods)) {
            return "Route::" . $type . '(\'' . $name . '\',\'' . trim($prefix) . '@' . $function . '\');';
        } else {
            $name = $name . ($name <> '/' ? '/' : '') . $this->removeType($this->camelToDashScore($function));
            $fullUrl = $name . (strlen($params) > 0 ? (Str::endsWith($name, '/') ? '' : '/') . $params : '');
            return "Route::" . $type . '(\'' . $fullUrl . '\',\'' . trim($prefix) . '@' . $function . '\');';
        }
    }

    private function getInfoFromRoute($route)
    {
        if (Str::startsWith($route, 'Route::controller(')) {
            $re = '/(?<=\()(.*?)(?=\))/';
            $result = '';
            preg_match_all($re, $route, $matches, PREG_SET_ORDER, 0);
            if (count($matches[0]) > 0) {
                $result = explode(',', $matches[0][1]);
                foreach ($result as $key => $r) {
                    $result[$key] = str_replace('\'', '', $r);
                }
            }
            if (count($result) > 1) {
                $result['url'] = $result[0];
                $result['classPath'] = $result[1];
                $result['classFullPath'] = $this->pathController . '\\' . trim($result[1]);
                unset($result[0]);
                unset($result[1]);
                return $result;
            }
            return false;
        } else {
            return false;
//            throw new \Exception("Please Give Correct Controller ;)");
        }
    }

    private function camelToDashScore($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));;
    }

    private function removeType($type)
    {
        if (Str::startsWith($type, 'get')) {
            return str_replace('get-', '', $type);
        } else if (Str::startsWith($type, 'post')) {
            return str_replace('post-', '', $type);
        }
        return false;
    }

    private function buildRouteParams($classFullPath, $functionName)
    {
        $params = [];
        $f = new \ReflectionClass($classFullPath);
        $methodInfo = $f->getMethod($functionName);
        $parameters = $methodInfo->getParameters();
        if (count($parameters) > 0) {
            foreach ($parameters as $parameter) {
                $params[] = '{' . $parameter->getName() . ($methodInfo->getParameters()[0]->isDefaultValueAvailable() ? '?' : '') . '}';
            }
        }
        return implode('/', $params);
    }
}