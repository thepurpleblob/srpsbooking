<?php

namespace SRPS\SantaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timetable
 * @ORM\Entity
 * @ORM\Table(name="timetable")
 */
class Timetable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;  
    
    /**
     *
     * @var type date
     * 
     * @ORM\Column(name="time", type="date")
     */    
    private $time;
    
    public function __construct() {
        $this->id = 0;
        $this->time = date();
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    public function __get($name) {
        return $this->name;
    }
    
    public function __isset($name) {
        return isset($this->name);
    }    
}

