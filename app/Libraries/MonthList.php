<?php

namespace Vanguard\Libraries;

/**
 * This class returns the month of the year in order
 * Gives an iterator that can return the months
 * NOTE: Months begin at 1, however arrays are zero indexed, so position needs to be decremented
 */
class MonthList implements \Iterator
{
    const MONTHS_OF_THE_YEAR = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 
        'September', 'October', 'November', 'December');
    
    private $position = 1;

    public function __construct() {
        $this->position = 1;
    }

    public function rewind() {
        $this->position = 1;
    }

    public function current() {
        return static::MONTHS_OF_THE_YEAR[$this->position-1];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset(static::MONTHS_OF_THE_YEAR[$this->position-1]);
    }
}
