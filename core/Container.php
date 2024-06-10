<?php

declare(strict_types=1);

namespace Core;

use Application\Components\Exceptions\NotFoundException;
use Closure;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var Container|null
     */
    protected static ?Container $instance = null;

    /**
     * @var array
     */
    private array $bindings = [];

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @return Container
     */
    public static function getInstance(): Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public static function setInstance(Container $container): Container
    {
        return static::$instance = $container;
    }

    /**
     * @param string $id
     * @throws NotFoundException
     * @return false|mixed
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException(__METHOD__ . ": Not Found ({$id})");
        }

        return call_user_func($this->bindings[$id]["closure"]);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    /**
     * @param string $id
     * @param Closure $closure
     * @return void
     */
    public function set(string $id, Closure $closure): void
    {
        $this->bind($id, $closure);
    }

    /**
     * @param string $id
     * @throws NotFoundException
     * @return mixed
     */
    public function getShared(string $id): mixed
    {
        if ($this->hasShared($id)) {
            return $this->instances[$id];
        }

        if (!$this->has($id)) {
            throw new NotFoundException(__METHOD__ . ": Not Found ({$id})");
        }

        $value = call_user_func($this->bindings[$id]["closure"]);

        if ($this->bindings[$id]["shared"] && $value) {
            $this->instances[$id] = $value;
        }

        return $value;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasShared(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    /**
     * @param string $id
     * @param Closure $closure
     * @return void
     */
    public function setShared(string $id, Closure $closure): void
    {
        $this->bind($id, $closure, true);
    }

    /**
     * @param string $id
     * @param Closure $closure
     * @param bool $shared
     * @return void
     */
    private function bind(string $id, Closure $closure, bool $shared = false): void
    {
        $this->remove($id);

        $this->bindings[$id] = [
            "closure" => $closure,
            "shared" => $shared
        ];
    }

    /**
     * @param string $id
     * @return void
     */
    private function remove(string $id): void
    {
        unset($this->bindings[$id], $this->instances[$id]);
    }
}
