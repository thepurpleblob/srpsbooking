<?php

namespace SRPS\SantaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Train
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SRPS\SantaBundle\Entity\TrainRepository")
 */
class Train
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
