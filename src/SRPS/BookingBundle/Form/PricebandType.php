<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PricebandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destination', 'text', array('read_only'=>true))
            ->add('first', 'money', array(
                'currency' => 'GBP',
            ))
            ->add('standard', 'money', array(
                'currency' => 'GBP',
            ))
            ->add('child', 'money', array(
                'currency' => 'GBP',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SRPS\BookingBundle\Entity\Priceband'
        ));
    }

    public function getName()
    {
        return 'srps_bookingbundle_pricebandtype';
    }
}
