<?php

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ORM entity for price bands
 *
 * @author howard
 * @ORM\Entity
 * @ORM\Table(name="priceband")
 */
class Priceband {
    
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
     * @ORM\Column(type="integer")
     */
    protected $destinationid;

    /**
     * @ORM\Column(type="integer")
     */    
    protected $pricebandgroupid;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */    
    protected $first;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */     
    protected $standard;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */     
    protected $child;
    
    /**
     * Convenience string for priceband form(s)
     * @var type string 
     */
    protected $destination;
    
    public function __construct() {
        $this->first = 0;
        $this->standard = 0;
        $this->child = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set serviceid
     *
     * @param integer $serviceid
     * @return Priceband
     */
    public function setServiceid($serviceid)
    {
        $this->serviceid = $serviceid;
    
        return $this;
    }

    /**
     * Get serviceid
     *
     * @return integer 
     */
    public function getServiceid()
    {
        return $this->serviceid;
    }

    /**
     * Set destinationid
     *
     * @param integer $destinationid
     * @return Priceband
     */
    public function setDestinationid($destinationid)
    {
        $this->destinationid = $destinationid;
    
        return $this;
    }

    /**
     * Get destinationid
     *
     * @return integer 
     */
    public function getDestinationid()
    {
        return $this->destinationid;
    }

    /**
     * Set first
     *
     * @param float $first
     * @return Priceband
     */
    public function setFirst($first)
    {
        $this->first = $first;
    
        return $this;
    }

    /**
     * Get first
     *
     * @return float 
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set standard
     *
     * @param float $standard
     * @return Priceband
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;
    
        return $this;
    }

    /**
     * Get standard
     *
     * @return float 
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Set child
     *
     * @param float $child
     * @return Priceband
     */
    public function setChild($child)
    {
        $this->child = $child;
    
        return $this;
    }

    /**
     * Get child
     *
     * @return float 
     */
    public function getChild()
    {
        return $this->child;
    }
    
    public function setDestination($destination) {
        $this->destination = $destination;
    }
    
    public function getDestination() {
        return $this->destination;
    }
    
    public function setPricebandgroupid($pricebandgroupid) {
        $this->pricebandgroupid = $pricebandgroupid;
    }
    
    public function getPricebandgroup() {
        return $this->pricebandgroup;
    }
}