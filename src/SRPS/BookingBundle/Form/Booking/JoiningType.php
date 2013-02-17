<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumbersType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('joining')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SRPS\BookingBundle\Entity\Purchase'
        ));
    }

    public function getName()
    {
        return 'srps_bookingbundle_booking_joiningtype';
    }
}
