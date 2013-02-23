<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonalType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', 'text', array(
                'max_length' => 12,
                'label' => 'Title',
                ))
            ->add('surname', 'text', array(
                'max_length' => 20,
                'required' => true,
                'label' => 'Surname',
            ))
            ->add('firstname', 'text', array(
                'max_length' => 20,
                'required' => true,
                'label' => 'Firstname',
            ))
            ->add('address1', 'text', array(
                'max_length' => 25,
                'required' => true,
                'label' => 'Address line 1',
            ))
            ->add('address2', 'text', array(
                'max_length' => 25,
                'label' => 'Address line 2',
            ))
            ->add('city', 'text', array(
                'max_length' => 25,
                'required' => true,
                'label' => 'Post town/city',
            ))
            ->add('county', 'text', array(
                'max_length' => 25,
                'required' => true,
                'label' => 'County',
            ))
            ->add('postcode', 'text', array(
                'max_length' => 8,
                'required' => true,
                'label' => 'Postcode',
            ))
            ->add('phone', 'text', array(
                'max_length' => 15,
                'label' => 'Telephone',
            ))
            ->add('email', 'email', array(
                'required' => true,
                'label' => 'Email',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SRPS\BookingBundle\Entity\Purchase',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'srps_bookingbundle_booking_personaltype';
    }
}
