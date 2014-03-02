<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    
    private $lock_username;
    
    public function __construct($lock_username=true) {
        $this->lock_username = $lock_username;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        // choices for roles
        $roles = array(
            'ROLE_ORGANISER' => 'ROLE_ORGANISER',            
            'ROLE_ADMIN' => 'ROLE_ADMIN',
        );
        
        $builder
            ->add('username', 'text', array(
                'read_only' => $this->lock_username,
            ))
            ->add('firstname')
            ->add('lastname')
            ->add('password', 'password')
            ->add('role', 'choice', array(
                'choices' => $roles,
            ))    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SRPS\BookingBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'srps_bookingbundle_usertype';
    }
}
