<?php

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\ExecutionContext;

/**
 * ORM entity for price bands
 *
 * @author howard
 * @ORM\Entity
 * @ORM\Table(name="pricebandgroup")
 * @UniqueEntity({"name", "serviceid"})
 */
class Pricebandgroup {
    
    protected $pricebands;
    
    protected $destination;
  
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */     
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */      
    protected $serviceid;
    
    /**
     * @ORM\Column(type="string", length=150)
     */     
    protected $name;
    
    public function __construct() {
        $this->pricebands = new ArrayCollection();
        $this->name = '';
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new NotBlank());       
    }  
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
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
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getServiceid() {
        return $this->serviceid;
    }
    
    public function setServiceid($serviceid) {
        $this->serviceid = $serviceid;
    }
}