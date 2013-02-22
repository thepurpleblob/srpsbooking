<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DestinationType extends AbstractType
{

    protected $destinations;

    public function __construct($destinations) {
        $this->destinations = $destinations;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Construct list
        $choices = array();
        foreach ($this->destinations as $destination) {
            $choices[$destination->getCrs()] = $destination->getName();
        }

        $builder
            ->add('destination', 'choice', array(
                'choices' => $choices,
                'expanded' => true,
                'label' => 'Select destination',
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
        return 'srps_bookingbundle_booking_destinationtype';
    }
}
