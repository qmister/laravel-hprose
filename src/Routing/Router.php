<?php

namespace whereof\laravel\Hprose\Routing;

use Doctrine\Instantiator\Instantiator;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class Router
{

    /**
     * @var array
     */
    protected $routers = [];

    /**
     * @var int
     */
    protected $allowMethods = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_STATIC;

    /**
     * @return array
     */
    public function getRouters()
    {
        return $this->routers;
    }

    /**
     * @param int $allowMethods
     * @return $this
     */
    public function allowMethods(int $allowMethods)
    {
        $this->allowMethods = $allowMethods;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     * @throws \ReflectionException
     */
    public function addPath($path)
    {
        if (is_dir($path) && $phpFiles = $this->files($path, 'php')) {
            foreach ($phpFiles as $file) {
                $this->add($this->phpFileNameSpace($file));
            }
        }
        return $this;
    }

    /**
     * @param $dir
     * @param $ext
     * @return array
     */
    protected  function files($dir, $ext = null)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException("The dir argument must be a directory: $dir");
        }
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
        $files    = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && (!is_null($ext) || in_array($file->getExtension(), (array)$ext))) {
                $files[] = $file->getRealPath();
            }
        }
        return $files;
    }

    /**
     * @param string $phpfile
     * @return mixed|string
     */
    protected function phpFileNameSpace($phpfile)
    {
        $namespace         = $class = '';
        $getting_namespace = $getting_class = false;
        foreach (token_get_all(file_get_contents($phpfile)) as $token) {
            if (is_array($token) && $token[0] == T_NAMESPACE) {
                $getting_namespace = true;
            }
            if (is_array($token) && $token[0] == T_CLASS) {
                $getting_class = true;
            }
            if ($getting_namespace === true) {
                if (is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {
                    $namespace .= $token[1];
                } elseif ($token === ';') {
                    $getting_namespace = false;
                }
            }
            if ($getting_class === true) {
                if (is_array($token) && $token[0] == T_STRING) {
                    $class = $token[1];
                    break;
                }
            }
        }
        return $namespace ? $namespace . '\\' . $class : $class;
    }


    /**
     * @param string|callable $action
     * @param string $alias
     * @param array $options
     * @return $this
     * @throws \ReflectionException
     */
    public function add($action, string $alias = '', array $options = [])
    {
        if ($action instanceof \Closure) {
            $ref    = new ReflectionFunction($action);
            $args   = $this->getRefParameterArr($ref->getParameters());
            $result = [
                'class'   => '',
                'alias'   => $alias,
                'method'  => '',
                'args'    => $args,
                'methods' => $alias . '(' . implode(',', $args) . ')',
            ];
            $this->addFunction($action, $result['alias'], $options);
            $this->routers[] = $result;
        } elseif (is_string($action) && class_exists($action)) {
            $ref = new ReflectionClass($action);
            foreach ($ref->getMethods($this->allowMethods) as $method) {
                $alias      = strtolower(str_replace('\\', '_', $action));
                $args       = $this->getRefParameterArr($ref->getMethod($method->getName())->getParameters());
                $methodName = $method->getName();
                $result     = [
                    'class'   => $action,
                    'method'  => $methodName,
                    'alias'   => $alias,
                    'args'    => $args,
                    'methods' => $alias . '->' . $methodName . '(' . implode(',', $args) . ')',
                ];
                $instance   = (new Instantiator())->instantiate($action);
                $this->addInstanceMethods($instance, $result['alias'], $options);
                $this->routers[] = $result;
            }
        }
        return $this;
    }

    /**
     * @param $object
     * @param $column
     * @return array
     */
    protected function getRefParameterArr($object, $column = 'name')
    {
        return array_column(json_decode(json_encode($object), true), $column);
    }

    /**
     * https://github.com/hprose/hprose-php/wiki/06-Hprose-%E6%9C%8D%E5%8A%A1%E5%99%A8.
     * @param $object
     * @param $alias
     * @param $options
     * @return Router
     */
    protected function addInstanceMethods($object, $alias, $options)
    {
        app('hprose.socket.server')->addInstanceMethods($object, '', $alias, $options);
        return $this;
    }

    /**
     * https://github.com/hprose/hprose-php/wiki/06-Hprose-%E6%9C%8D%E5%8A%A1%E5%99%A8.
     * @param callable $action
     * @param string $alias
     * @param array $options
     *
     * @return Router
     */
    protected function addFunction(callable $action, string $alias, array $options)
    {
        app('hprose.socket.server')->addFunction($action, $alias, $options);
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Router
     */
    public function __call($name, $arguments)
    {
        app('hprose.socket.server')->$name(...$arguments);
        return $this;
    }
}
