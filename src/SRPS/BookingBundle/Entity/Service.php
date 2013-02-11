<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Service describes the main data
 * for a bookable train service
 *
 * @author howard
 * @ORM\Entity
 * @ORM\Table(name="service")
 * @UniqueEntity("code")
 * @UniqueEntity("name")
 */
class Service {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $code;
    
    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="text")
     */    
    protected $description;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $visible;
    
    /**
     * @ORM\Column(type="date")
     */
    protected $date;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $mealaname;
    
    /**
     * @ORM\Column( type="boolean")
     */
    protected $mealavisible;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $mealaprice;
   
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $mealbname;
    
    /**
     * @ORM\Column( type="boolean")
     */
    protected $mealbvisible;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $mealbprice;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $mealcname;
    
    /**
     * @ORM\Column( type="boolean")
     */
    protected $mealcvisible;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $mealcprice;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $mealdname;
    
    /**
     * @ORM\Column( type="boolean")
     */
    protected $mealdvisible;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $mealdprice;
    
    /**
     * constructor - set defaults
     */
    public function __construct() {
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->visible = 1;
        $this->date = new \DateTime();
        $this->mealaname = 'Breakfast';
        $this->mealbname = 'Lunch';
        $this->mealcname = 'Dinner';
        $this->mealdname = 'Not used';
        $this->mealaprice = 0;
        $this->mealbprice = 0;
        $this->mealcprice = 0;
        $this->mealdprice = 0;
        $this->mealavisible = 0;
        $this->mealbvisible = 0;
        $this->mealcvisible = 0;
        $this->mealdvisible = 0;
    }

    /**
     * Validation stuff
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('code', new NotBlank());        
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('description', new NotBlank()); 
        $metadata->addPropertyConstraint('mealaname', new NotBlank()); 
        $metadata->addPropertyConstraint('mealbname', new NotBlank());
        $metadata->addPropertyConstraint('mealcname', new NotBlank());
        $metadata->addPropertyConstraint('mealdname', new NotBlank());
        $metadata->addPropertyConstraint('mealaprice', new NotBlank()); 
        $metadata->addPropertyConstraint('mealbprice', new NotBlank());
        $metadata->addPropertyConstraint('mealcprice', new NotBlank());
        $metadata->addPropertyConstraint('mealdprice', new NotBlank());        
        $metadata->addPropertyConstraint('date', new Assert\Date());        
    }
    
    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Service
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

    /**
     * Set description
     *
     * @param string $description
     * @return Service
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
     * Set visible
     *
     * @param boolean $visible
     * @return Service
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Service
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set mealaname
     *
     * @param string $mealaname
     * @return Service
     */
    public function setMealaname($mealaname)
    {
        $this->mealaname = $mealaname;
    
        return $this;
    }

    /**
     * Get mealaname
     *
     * @return string 
     */
    public function getMealaname()
    {
        return $this->mealaname;
    }

    /**
     * Set mealavisible
     *
     * @param boolean $mealavisible
     * @return Service
     */
    public function setMealavisible($mealavisible)
    {
        $this->mealavisible = $mealavisible;
    
        return $this;
    }

    /**
     * Get mealavisible
     *
     * @return boolean 
     */
    public function getMealavisible()
    {
        return $this->mealavisible;
    }

    /**
     * Set mealaprice
     *
     * @param float $mealaprice
     * @return Service
     */
    public function setMealaprice($mealaprice)
    {
        $this->mealaprice = $mealaprice;
    
        return $this;
    }

    /**
     * Get mealaprice
     *
     * @return float 
     */
    public function getMealaprice()
    {
        return $this->mealaprice;
    }

    /**
     * Set mealbname
     *
     * @param string $mealbname
     * @return Service
     */
    public function setMealbname($mealbname)
    {
        $this->mealbname = $mealbname;
    
        return $this;
    }

    /**
     * Get mealbname
     *
     * @return string 
     */
    public function getMealbname()
    {
        return $this->mealbname;
    }

    /**
     * Set mealbvisible
     *
     * @param boolean $mealbvisible
     * @return Service
     */
    public function setMealbvisible($mealbvisible)
    {
        $this->mealbvisible = $mealbvisible;
    
        return $this;
    }

    /**
     * Get mealbvisible
     *
     * @return boolean 
     */
    public function getMealbvisible()
    {
        return $this->mealbvisible;
    }

    /**
     * Set mealbprice
     *
     * @param float $mealbprice
     * @return Service
     */
    public function setMealbprice($mealbprice)
    {
        $this->mealbprice = $mealbprice;
    
        return $this;
    }

    /**
     * Get mealbprice
     *
     * @return float 
     */
    public function getMealbprice()
    {
        return $this->mealbprice;
    }

    /**
     * Set mealcname
     *
     * @param string $mealcname
     * @return Service
     */
    public function setMealcname($mealcname)
    {
        $this->mealcname = $mealcname;
    
        return $this;
    }

    /**
     * Get mealcname
     *
     * @return string 
     */
    public function getMealcname()
    {
        return $this->mealcname;
    }

    /**
     * Set mealcvisible
     *
     * @param boolean $mealcvisible
     * @return Service
     */
    public function setMealcvisible($mealcvisible)
    {
        $this->mealcvisible = $mealcvisible;
    
        return $this;
    }

    /**
     * Get mealcvisible
     *
     * @return boolean 
     */
    public function getMealcvisible()
    {
        return $this->mealcvisible;
    }

    /**
     * Set mealcprice
     *
     * @param float $mealcprice
     * @return Service
     */
    public function setMealcprice($mealcprice)
    {
        $this->mealcprice = $mealcprice;
    
        return $this;
    }

    /**
     * Get mealcprice
     *
     * @return float 
     */
    public function getMealcprice()
    {
        return $this->mealcprice;
    }

    /**
     * Set mealdname
     *
     * @param string $mealdname
     * @return Service
     */
    public function setMealdname($mealdname)
    {
        $this->mealdname = $mealdname;
    
        return $this;
    }

    /**
     * Get mealdname
     *
     * @return string 
     */
    public function getMealdname()
    {
        return $this->mealdname;
    }

    /**
     * Set mealdvisible
     *
     * @param boolean $mealdvisible
     * @return Service
     */
    public function setMealdvisible($mealdvisible)
    {
        $this->mealdvisible = $mealdvisible;
    
        return $this;
    }

    /**
     * Get mealdvisible
     *
     * @return boolean 
     */
    public function getMealdvisible()
    {
        return $this->mealdvisible;
    }

    /**
     * Set mealdprice
     *
     * @param float $mealdprice
     * @return Service
     */
    public function setMealdprice($mealdprice)
    {
        $this->mealdprice = $mealdprice;
    
        return $this;
    }

    /**
     * Get mealdprice
     *
     * @return float 
     */
    public function getMealdprice()
    {
        return $this->mealdprice;
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
     * Set code
     *
     * @param string $code
     * @return Service
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }
}