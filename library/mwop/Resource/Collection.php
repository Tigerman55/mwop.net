<?php
namespace mwop\Resource;

use mwop\Stdlib\ResourceCollection,
    Iterator;

class Collection implements ResourceCollection
{
    protected $count;
    protected $items;
    protected $class;
    protected $objects = array();

    public function __construct($items, $class)
    {
        $this->items = $items;
        $this->class = $class;
        $this->count = count($items);
    }

    public function count()
    {
        return $this->count;
    }

    public function current()
    {
        $item = current($this->items);
        if ($item === false) {
            return false;
        }
        $key  = $this->key();
        if (!isset($this->objects[$key])) {
            $object = new $this->class();
            $object->fromArray($item);
            $this->objects[$key] = $object;
        }
        return $this->objects[$key];
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }

    public function rewind()
    {
        reset($this->items);
    }
}