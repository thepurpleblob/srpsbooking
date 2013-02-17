<?php

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Description of Booking
 *
 * @author howard
 * @ORM\Entity
 * @ORM\Table(name="purchase")
 * @UniqueEntity("bookingref")
 */
class Purchase {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="integer")
     */
    protected $created;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $seskey;

    /**
     * @ORM\Column(type="string", length=1)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $bookingref;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $completed;

    /**
     * @ORM\Column(type="string", length=15)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $surname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $address1;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $address2;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $county;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $postcode;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=5)
     */
    protected $joining;

    /**
     * @ORM\Column(type="string", length=5)
     */
    protected $destination;

    /**
     * @ORM\Column(type="string", length=1)
     */
    protected $class;

    /**
     * @ORM\Column(type="integer")
     */
    protected $adults;

    /**
     * @ORM\Column(type="integer")
     */
    protected $children;

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
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $payment;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    public function __construct() {
        $this->type = 'O';
        $this->code = '';
        $this->bookingref = '';
        $this->completed = false;
        $this->title = 'Mr';
        $this->surname = '';
        $this->firstname = '';
        $this->address1 = '';
        $this->address2 = '';
        $this->city = '';
        $this->county = '';
        $this->postcode = '';
        $this->phone = '';
        $this->email = '';
        $this->joining = '';
        $this->destination = '';
        $this->class = '';
        $this->adults = 2;
        $this->children = 0;
        $this->meala = 0;
        $this->mealb = 0;
        $this->mealc = 0;
        $this->meald = 0;
        $this->payment = 0;
        $this->date = new \DateTime();
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
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Booking
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    public function getCreated() {
        return $this->created;
    }

    /**
     * Set key
     *
     * @param string $seskey
     * @return Booking
     */
    public function setSeskey($seskey)
    {
        $this->seskey = $seskey;

        return $this;
    }

    /**
     * Get seskey
     *
     * @return string
     */
    public function getSeskey()
    {
        return $this->seskey;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Booking
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Booking
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set bookingref
     *
     * @param string $bookingref
     * @return Booking
     */
    public function setBookingref($bookingref)
    {
        $this->bookingref = $bookingref;

        return $this;
    }

    /**
     * Get bookingref
     *
     * @return string
     */
    public function getBookingref()
    {
        return $this->bookingref;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Booking
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Booking
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Booking
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return Booking
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return Booking
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Booking
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set county
     *
     * @param string $county
     * @return Booking
     */
    public function setCounty($county)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * Get county
     *
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return Booking
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Booking
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Booking
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set joining
     *
     * @param string $joining
     * @return Booking
     */
    public function setJoining($joining)
    {
        $this->joining = $joining;

        return $this;
    }

    /**
     * Get joining
     *
     * @return string
     */
    public function getJoining()
    {
        return $this->joining;
    }

    /**
     * Set destination
     *
     * @param string $destination
     * @return Booking
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return Booking
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set adults
     *
     * @param integer $adults
     * @return Booking
     */
    public function setAdults($adults)
    {
        $this->adults = $adults;

        return $this;
    }

    /**
     * Get adults
     *
     * @return integer
     */
    public function getAdults()
    {
        return $this->adults;
    }

    /**
     * Set children
     *
     * @param integer $children
     * @return Booking
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return integer
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set meala
     *
     * @param integer $meala
     * @return Booking
     */
    public function setMeala($meala)
    {
        $this->meala = $meala;

        return $this;
    }

    /**
     * Get meala
     *
     * @return integer
     */
    public function getMeala()
    {
        return $this->meala;
    }

    /**
     * Set mealb
     *
     * @param integer $mealb
     * @return Booking
     */
    public function setMealb($mealb)
    {
        $this->mealb = $mealb;

        return $this;
    }

    /**
     * Get mealb
     *
     * @return integer
     */
    public function getMealb()
    {
        return $this->mealb;
    }

    /**
     * Set mealc
     *
     * @param integer $mealc
     * @return Booking
     */
    public function setMealc($mealc)
    {
        $this->mealc = $mealc;

        return $this;
    }

    /**
     * Get mealc
     *
     * @return integer
     */
    public function getMealc()
    {
        return $this->mealc;
    }

    /**
     * Set meald
     *
     * @param integer $meald
     * @return Booking
     */
    public function setMeald($meald)
    {
        $this->meald = $meald;

        return $this;
    }

    /**
     * Get meald
     *
     * @return integer
     */
    public function getMeald()
    {
        return $this->meald;
    }

    /**
     * Set payment
     *
     * @param float $payment
     * @return Booking
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return float
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Booking
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
}