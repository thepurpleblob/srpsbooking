<?php

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ORM entity for stations
 *
 * @author howard
 * @ORM\Entity
 * @ORM\Table(name="station")
 * @UniqueEntity("crs")
 * @UniqueEntity("name");
 */
class Station {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $crs;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $name;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCrs() {
        return $this->crs;
    }

    public function setCrs($crs) {
        $this->crs = $crs;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
}

?>
