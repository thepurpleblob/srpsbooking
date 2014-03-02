<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JoiningType extends AbstractType
{

    protected $stations;

    public function __construct($stations) {
        $this->stations = $stations;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Construct list
        $choices = array();
        foreach ($this->stations as $station) {
            $choices[$station->getCrs()] = $station->getStation();
        }

        $builder
            ->add('joining', 'choice', array(
                'choices' => $choices,
                'expanded' => true,
                'label' => 'Select station',
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
        return 'srps_bookingbundle_booking_joiningtype';
    }
}
