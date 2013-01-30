<?php

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;;
use Doctrine\Common\Collections\ArrayCollection;

class Pricebands {
    
    protected $pricebands;
    
    protected $destination;
    
    public function __construct() {
        $this->pricebands = new ArrayCollection();
    }
    
    public function getPricebands() {
        return $this->pricebands;
    }
    
    public function setPricebands($pricebands) {
        $this->pricebands = $pricebands;
    }
    
    public function getDestination() {
        return $this->destination;
    }
    
    public function setDestination($destination) {
        $this->destination = $destination;
    }
}