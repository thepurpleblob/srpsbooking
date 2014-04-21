<?php

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ORM entity for multiple destinations
 *
 * @author howard
 * @ORM\Entity
 * @ORM\Table(name="destination")
 * @UniqueEntity({"name", "serviceid"})
 */
class Destination {

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

    /**
     * @ORM\Column(type="string", length=5)
     */
    protected $crs;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     */
    protected $bookinglimit;

    /**
     * constructor - set defaults
     */
    public function __construct() {
        $this->id = 0;
        $this->name = '';
        $this->crs = '';
        $this->description = '';
        $this->bookinglimit = 0;
    }

    /**
     * Validation stuff
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('crs', new NotBlank());
        $metadata->addPropertyConstraint('description', new NotBlank());
    }
    
    /**
     * Set id
     */
    public function setId($id) {
        $this->id = $id;
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
     * Set name
     *
     * @param string $name
     * @return Destination
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setCrs($crs) {
        $this->crs = $crs;
    }

    public function getCrs() {
        return $this->crs;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Destination
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set serviceid
     *
     * @param integer $serviceid
     * @return Destination
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

    public function getUsed() {
        return $this->used;
    }

    public function setUsed($used) {
        $this->used = $used;
    }

    public function getBookinglimit() {
        return $this->bookinglimit;
    }

    public function setBookinglimit($bookinglimit) {
        $this->bookinglimit = $bookinglimit;
    }

}