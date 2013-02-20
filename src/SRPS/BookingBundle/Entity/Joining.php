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
 * ORM entity for joining stations
 *
 * @author howard
 * @ORM\Entity
 * @ORM\Table(name="joining")
 * @UniqueEntity({"station", "serviceid"})
 */

class Joining
{
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
    protected $pricebandgroupid;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $station;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $crs;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $meala;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $mealb;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $mealc;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $meald;

    protected $pricebandname;

    public function __construct() {
        $this->station = '';
        $this->crs = '';
        $this->meala = true;
        $this->mealb = true;
        $this->mealc = true;
        $this->meald = true;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('station', new NotBlank());
        $metadata->addPropertyConstraint('crs', new NotBlank());
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getServiceid() {
        return $this->serviceid;
    }

    public function setServiceid($serviceid) {
        $this->serviceid = $serviceid;
    }

    public function getPricebandgroupid() {
        return $this->pricebandgroupid;
    }

    public function setPricebandgroupid($pricebandgroupid) {
        $this->pricebandgroupid = $pricebandgroupid;
    }

    public function getStation() {
        return $this->station;
    }

    public function setStation($station) {
        $this->station = $station;
    }

    public function getCrs() {
        return $this->crs;
    }

    public function setCrs($crs) {
        $this->crs = $crs;
    }

    public function getPricebandname() {
        return $this->pricebandname;
    }

    public function setPricebandname($pricebandname) {
        $this->pricebandname = $pricebandname;
    }

    public function isMeala() {
        return $this->meala;
    }

    public function setMeala($meala) {
        $this->meala = $meala;
    }

    public function isMealb() {
        return $this->mealb;
    }

    public function setMealb($mealb) {
        $this->mealb = $mealb;
    }

    public function isMealc() {
        return $this->mealc;
    }

    public function setMealc($mealc) {
        $this->mealc = $mealc;
    }

    public function isMeald() {
        return $this->meald;
    }

    public function setMeald($meald) {
        $this->meald = $meald;
    }
}
