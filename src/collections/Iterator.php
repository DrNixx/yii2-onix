<?php
namespace onix\collections;

class Iterator implements \Iterator
{
    /**
     * Iterate on pairs
     */
    const PAIRS = 0;

    /**
     * Iterate on keys
     */
    const KEYS = 1;

    /**
     * Iterate on values
     */
    const VALUES = 2;

    /**
     * @var integer Type: self::PAIRS, self::KEYS or self::VALUES
     */
    private $type;

    /**
     * @var integer Index
     */
    private $index;

    /**
     * @var SortedMapInterface Map
     */
    private $map;

    /**
     * Constructor
     *
     * @param SortedMapInterface $map   Sorted map
     * @param integer $type Iterator type
     */
    protected function __construct($map, $type)
    {
        $this->map = $map;
        $this->type = $type;
        $this->rewind();
    }

    /**
     * Create a new iterator on pairs
     * @param SortedMapInterface $map Sorted map
     * @return Iterator A new iterator on pairs
     */
    public static function create($map)
    {
        return new static($map, self::PAIRS);
    }

    /**
     * Create a new iterator on keys
     * @param SortedMapInterface $map Sorted map
     * @return  Iterator  A new iterator on keys
     */
    public static function keys($map)
    {
        return new static($map, self::KEYS);
    }

    /**
     * Create a new iterator on values
     * @param SortedMapInterface $map Sorted map
     * @return  Iterator  A new iterator on values
     */
    public static function values($map)
    {
        return new static($map, self::VALUES);
    }

    /**
     * @var     TreeNode  The current node
     */
    protected $current;

    /**
     * Rewind the Iterator to the first element
     * @return  void
     */
    public function rewind()
    {
        $this->index = 0;

        try {
            $this->current = $this->map->first();
        } catch (\OutOfBoundsException $e) {
            $this->current = null;
        }
    }

    /**
     * Return the current key
     * @return  mixed  The current key
     */
    public function key()
    {
        if ($this->type == self::PAIRS) {
            return $this->current->key;
        } else {
            return $this->index;
        }
    }

    /**
     * Return the current value
     * @return  mixed  The current value
     */
    public function current()
    {
        if ($this->type == self::KEYS) {
            return $this->current->key;
        } else {
            return $this->current->value;
        }
    }

    /**
     * Move forward to the next element
     * @return  void
     */
    public function next()
    {
        try {
            $this->current = $this->map->successor($this->current);
        } catch (\OutOfBoundsException $e) {
            $this->current = null;
        }

        $this->index++;
    }

    /**
     * Checks if current position is valid
     * @return  boolean
     */
    public function valid()
    {
        return (bool) $this->current;
    }
}
