<?php

 

/**
 * Description of Paginate
 *
 * @file    Paginate
 * @author  ryan <cumt.xiaochi@gmail.com>
 * @created Jul 24, 2012 11:56:27 AM
 */
class Paginate {

    private $total = null;
    private $per_page = 10;
    private $offset = null;

    function __construct($per_page=10, $total=0, $offset=0) {
        $this->per_page = $per_page;
        $this->total = $total;
        $this->offset = $offset;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function setPerPage($per_page) {
        $this->per_page = $per_page;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
    }

    public function hasPrevious() {
        return $this->offset > 0;
    }

    public function hasNext() {
        return ($this->offset + $this->per_page) < $this->total; // ????
    }

    public function previousOffset() {
        $prev = $this->offset - $this->per_page;
        return ($prev < 0)? 0 : $prev;
    }

    public function nextOffset() {
        $next = $this->offset + $this->per_page;
        return ($next > $this->total-1)? $this->total-1 : $next;
    }
}

?>
