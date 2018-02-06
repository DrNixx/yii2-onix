<?php
namespace onix\collections;

/**
 * Array list that is a section of a parent array list. Do not create this
 * object anywhere other than in `ArrayList::subList()`.
 */
final class SubArrayList extends ArrayList
{
    /**
     * @var ArrayList
     */
    private $parent;
    private $fromIndex;

    public static function factory(ArrayList $parent, $fromIndex, $toIndex)
    {
        $subList = new self(array_values(
            array_slice($parent->toArray(), $fromIndex, $toIndex - $fromIndex, true)
        ));
        $subList->parent = $parent;
        $subList->fromIndex = $fromIndex;
        return $subList;
    }

    public function add($element)
    {
        $this->parent->insert($this->fromIndex + $this->count(), $element);
        return $this;
    }

    public function addAll($elements)
    {
        $this->parent->insertAll($this->fromIndex + $this->count(), $elements);
        return $this;
    }

    public function insert($index, $element, $fromParent = false)
    {
        if (true === $fromParent) {
            $thisIndex = $index - $this->fromIndex;
            if (0 <= $thisIndex && $this->count() >= $thisIndex) {
                parent::insert($thisIndex, $element);
            }
        } else {
            $this->parent->insert($this->fromIndex + $index, $element);
        }
        return $this;
    }

    public function insertAll($index, $elements, $fromParent = false)
    {
        if (true === $fromParent) {
            $thisIndex = $index - $this->fromIndex;
            if (0 <= $thisIndex && $this->count() >= $thisIndex) {
                parent::insertAll($thisIndex, $elements);
            }
        } else {
            $this->parent->insertAll($this->fromIndex + $index, $elements);
        }
        return $this;
    }

    public function set($index, $element, $fromParent = false)
    {
        if (true === $fromParent) {
            $thisIndex = $index - $this->fromIndex;
            if (0 <= $thisIndex && $this->count() > $thisIndex) {
                parent::set($thisIndex, $element);
            }
        } else {
            $this->parent->set($index + $this->fromIndex, $element);
        }
        return $this;
    }


    public function get($index)
    {
        if (0 > $index && $this->count() > $index) {
            throw new \OutOfBoundsException($index);
        }
        return $this->parent->get($index + $this->fromIndex);
    }

    public function remove($element)
    {
        $key = array_search($element, $this->toArray(), true);
        if (false !== $key) {
            $this->parent->drop($key + $this->fromIndex);
        }
        return $this;
    }

    public function removeAll($elements, $fromParent = false)
    {
        if (true === $fromParent) {
            parent::removeAll($elements);
        } else {
            $this->parent->removeAll($elements);
        }
        return $this;
    }

    public function drop($index, $fromParent = false)
    {
        if (true === $fromParent) {
            $thisIndex = $index - $this->fromIndex;
            if (0 <= $thisIndex && $this->count() > $thisIndex) {
                parent::drop($thisIndex);
            }
        } else {
            $this->parent->drop($index + $this->fromIndex);
        }
        return $this;
    }

    public function retainAll($elements)
    {
        // TODO
    }

    public function clear($fromParent = false)
    {
        if (true === $fromParent) {
            parent::clear();
        } else {
            $loops = $this->count();
            for ($i = 0; $i < $loops; $i++) {
                $this->parent->drop($this->fromIndex);
            }
        }
        return $this;
    }
}
