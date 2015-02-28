<?php
/**
 * User: Jeremy
 * Date: 2/28/2015
 * Time: 9:56 AM
 */

namespace JeremyGiberson\Entropy\Iterator;


class MutableIterator implements \Iterator, \ArrayAccess, \Countable
{
    /** @var  array */
    protected $collection;
    /** @var  array */
    protected $previous_keys;
    /** @var  array */
    protected $next_keys;
    /** @var  mixed */
    protected $current_key;

    /**
     * @param array $collection
     */
    function __construct(array $collection = [])
    {
        $this->collection = $collection;
        $this->rewind();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->collection[$this->current_key];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->previous_keys[] = $this->current_key;
        $this->current_key = array_shift($this->next_keys);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->current_key;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->collection[$this->current_key]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->previous_keys = [];
        $this->next_keys = array_keys($this->collection);
        $this->current_key = array_shift($this->next_keys);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->collection[$offset] = $value;
        // if new key, add to end
        if(!in_array($offset, $this->next_keys)) {
            $this->next_keys[] = $offset;
            // if we were at an invalid position (end) then
            // set current position to the last element
            if(!$this->valid())
            {
                $this->next();
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
        // if we've yet iterated over the key, remove it from pending iteration
        if(in_array($offset, $this->next_keys))
        {
            $index = array_search($offset, $this->next_keys);
            unset($this->next_keys[$index]);
        } elseif (in_array($offset, $this->previous_keys))
        {
            // doesn't affect iterating, but for cleanup sake
            $index = array_search($offset, $this->previous_keys);
            unset($this->previous_keys[$index]);
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * Return the first key of the iterator
     * @return mixed
     */
    public function first_key()
    {
        reset($this->collection);
        return key($this->collection);
    }

    /**
     * Return the first value of the iterator
     * @return mixed
     */
    public function first()
    {
        reset($this->collection);
        return current($this->collection);
    }

    /**
     * Return the last key of the iterator
     * @return mixed
     */
    public function last_key()
    {
        end($this->collection);
        return key($this->collection);
    }

    /**
     * Return the last value of the iterator
     * @return mixed
     */
    public function last()
    {
        end($this->collection);
        return current($this->collection);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->collection;
    }

    /**
     * Remove all occurrences of value from the collection
     * @param $value
     */
    public function remove($value)
    {
        foreach($this->collection as $key => $v)
        {
            if($value === $v)
            {
                unset($this->collection[$key]);
            }
        }
    }
}