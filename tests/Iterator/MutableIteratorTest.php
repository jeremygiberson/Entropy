<?php
/**
 * User: Jeremy
 * Date: 2/28/2015
 * Time: 10:10 AM
 */

namespace Entropy\Tests\Iterator;


use JeremyGiberson\Entropy\Iterator\MutableIterator;

class MutableIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_foreach_empty()
    {
        $iterator = new MutableIterator();
        $iterations = 0;
        foreach($iterator as $v)
        {
            $iterations++;
        }
        $this->assertEquals(0, $iterations,
            'should not iterate over an empty iterator');
    }

    public function test_foreach()
    {
        $iterator = new MutableIterator(range(1,10));
        $str = '';
        foreach($iterator as $value)
        {
            $str .= $value;
        }
        $this->assertEquals('12345678910', $str,
            'foreach should iterate over the collection');
    }

    public function test_count()
    {
        $iterator = new MutableIterator(range(1, $count = rand(2,10)));
        $this->assertEquals($count, count($iterator),
            'count should return number of items in iterator');
    }

    public function test_isset()
    {
        $iterator = new MutableIterator(['foo' => 'bar']);
        $this->assertTrue(isset($iterator['foo']),
            'isset should return true for a key in the iterator');
        $this->assertFalse(isset($iterator['bar']),
            'isset should return false for a key not in the iterator');
    }

    /**
     * @depends test_isset
     */
    public function test_unset()
    {
        $iterator = new MutableIterator(['foo' => 'bar']);
        $this->assertTrue(isset($iterator['foo']),
            'isset should return true for a key in the iterator');
        unset($iterator['foo']);
        $this->assertFalse(isset($iterator['foo']),
            'isset should return false for a key no longer in the iterator');
    }

    public function test_offsetGet()
    {
        $iterator = new MutableIterator(['foo' => $v = uniqid()]);
        $this->assertEquals($v, $iterator['foo']);
    }

    /**
     * @depends test_isset
     * @depends test_offsetGet
     */
    public function test_offsetSet()
    {
        $iterator = new MutableIterator();
        $this->assertFalse(isset($iterator['foo']),
            'item should not be in the iterator');
        $iterator['foo'] = $v = uniqid();
        $this->assertEquals($v, $iterator['foo'],
            'item should be in the iterator');
    }

    public function test_first_and_first_key()
    {
        $iterator = new MutableIterator(['foo' => 'bar', 'biz' => 'buz']);
        $this->assertEquals('foo', $iterator->first_key(),
            'should return first key of iterator');
        $this->assertEquals('bar', $iterator->first(),
            'should return first value of the iterator');
    }

    public function test_last_and_last_key()
    {
        $iterator = new MutableIterator(['foo' => 'bar', 'biz' => 'buz']);
        $this->assertEquals('biz', $iterator->last_key(),
            'should return last key of iterator');
        $this->assertEquals('buz', $iterator->last(),
            'should return last value of an iterator');
    }

    /**
     * @depends test_count
     * @depends test_unset
     */
    public function test_foreach_mutating_behind()
    {
        $iterator = new MutableIterator(range(1,10));
        $lastKey = null;
        $iterations = 0;
        foreach($iterator as $key => $value)
        {
            $iterations++;
            if($lastKey !== null)
            {
                unset($iterator[$lastKey]);
            }
            $lastKey = $key;
        }

        $this->assertEquals(10, $iterations,
            'mutation should not have affected number of iterations');
        $this->assertCount(1, $iterator,
            'all but the last item should be removed from iterator');
    }

    /**
     * @depends test_count
     * @depends test_unset
     */
    public function test_foreach_mutating_current()
    {
        $iterator = new MutableIterator(range(1,10));
        $iterations = 0;
        foreach($iterator as $key => $value)
        {
            $iterations++;
            unset($iterator[$key]);
        }
        $this->assertEquals(10, $iterations,
            'mutation should not have affected number of iterations');
        $this->assertCount(0, $iterator,
            'all items should be removed from the iterator');
    }

    /**
     * @depends test_count
     * @depends test_unset
     */
    public function test_foreach_mutating_ahead()
    {
        $iterator = new MutableIterator(range(1,10));
        $iterations = 0;
        foreach($iterator as $key => $value)
        {
            $iterations++;
            if($key != $iterator->last_key())
            {
                unset($iterator[$key + 1]);
            }
        }
        $this->assertEquals(5, $iterations,
            'mutation should have affected number of iterations');
        $this->assertCount(5, $iterator,
            'half the items should be removed from the iterator');
    }

    public function test_toArray()
    {
        $iterator = new MutableIterator($r = range(1,10));
        $this->assertEquals($r, $iterator->toArray(),
            'toArray should return iterator collection');
    }

    /**
     * @depends test_toArray
     */
    public function test_remove()
    {
        $iterator = new MutableIterator(['foo','biz','baz']);
        $iterator->remove('biz');
        $this->assertEquals([0 => 'foo', 2 => 'baz'], $iterator->toArray(),
            'biz should be removed from the collection');
    }
}