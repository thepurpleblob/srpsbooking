<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClassType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // choices
        $choices = array(
            'F' => 'First class',
            'S' => 'Standard class'
        );

        $builder
            ->add('class', 'choice', array(
            'choices' => $choices,
            'expanded' => true,
        ));
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
        return 'srps_bookingbundle_booking_classtype';
    }
}
