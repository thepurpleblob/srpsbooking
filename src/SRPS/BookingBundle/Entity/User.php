<?php

namespace SRPS\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SRPS\BookingBundle\Entity\User
 *
 * @ORM\Table(name="srps_users")
 * @ORM\Entity
 * @UniqueEntity("username");
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;
    
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstname;    
    
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $lastname;    

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=60)
     */
    private $role;    

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = '';
        $this->firstname = '';
        $this->lastname = '';
        $this->role = 'ROLE_ORGANISER';
        $this->email = '';
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('firstname', new NotBlank());
        $metadata->addPropertyConstraint('lastname', new NotBlank());
        $metadata->addPropertyConstraint('username', new NotBlank()); 
        $metadata->addPropertyConstraint('role', new NotBlank());
        $metadata->addPropertyConstraint('password', new NotBlank());        
    }    

    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username) {
        $this->username = $username;
    } 

    public function getSalt()
    {
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password) {
        
        // do not update empty password
        if ($password) {
            $this->password = md5($password);
        }    
    }

    public function getRoles()
    {
        return array($this->role);
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole($role) {
        $this->role = $role;
    }    
    
    public function isActive() {
        return $this->isActive;
    }
    
    public function setActive($active) {
        $this->isActive = $active;
    }
    
    public function getFirstname() {
        return $this->firstname;
    }
    
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }
    
    public function getLastname() {
        return $this->lastname;
    }
    
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }
    /**
     * @inheritDoc
     */
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }
}
