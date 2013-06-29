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
 * @ORM\Table(name="limits")
 */
class Limits
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
    protected $first;

    /**
     * @ORM\Column(type="integer")
     */
    protected $standard;

    /**
     * @ORM\Column(type="integer")
     */
    protected $firstsingles;

    /**
     * @ORM\Column(type="integer")
     */
    protected $meala;

    /**
     * @ORM\Column(type="integer")
     */
    protected $mealb;

    /**
     * @ORM\Column(type="integer")
     */
    protected $mealc;

    /**
     * @ORM\Column(type="integer")
     */
    protected $meald;

    /**
     * @ORM\Column(type="integer")
     */
    protected $maxparty;

    /**
     * Array holds destination(s) limits for limits form
     */
    protected $destinationlimits;

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

    public function getFirst() {
        return $this->first;
    }

    public function setFirst($first) {
        $this->first = $first;
    }

    public function getStandard() {
        return $this->standard;
    }

    public function setFirstsingles($firstsingles) {
        $this->firstsingles = $firstsingles;
    }

    public function getFirstsingles() {
        return $this->firstsingles;
    }

    public function setStandard($standard) {
        $this->standard = $standard;
    }

    public function getMeala() {
        return $this->meala;
    }

    public function setMeala($meala) {
        $this->meala = $meala;
    }

    public function getMealb() {
        return $this->mealb;
    }

    public function setMealb($mealb) {
        $this->mealb = $mealb;
    }

    public function getMealc() {
        return $this->mealc;
    }

    public function setMealc($mealc) {
        $this->mealc = $mealc;
    }

    public function getMeald() {
        return $this->meald;
    }

    public function setMeald($meald) {
        $this->meald = $meald;
    }

    public function getMaxparty() {
        return $this->maxparty;
    }

    public function setMaxparty($maxparty) {
        $this->maxparty = $maxparty;
    }

    public function getDestinationlimits() {
        return $this->destinationlimits;
    }

    public function setDestinationlimits($destinationlimits) {
        $this->destinationlimits = $destinationlimits;
    }

    public function __construct() {
        $this->first = 0;
        $this->standard = 0;
        $this->firstsingles = 0;
        $this->child = 0;
        $this->meala = 0;
        $this->mealb = 0;
        $this->mealc = 0;
        $this->meald = 0;
        $this->maxparty = 16;
        $this->destinationlimits = array();
    }
}
