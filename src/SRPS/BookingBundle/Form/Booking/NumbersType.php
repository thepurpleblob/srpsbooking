<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumbersType extends AbstractType
{

    private function numbersChoices($none=false) {
        if ($none) {
            $choices = array(0 => 'None');
        } else {
            $choices = array();
        }
        for ($i=1; $i<=16; $i++) {
            $choices[$i] = "$i";
        }

        return $choices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adults', 'choice', array(
                'choices' => $this->numbersChoices(),
                'label' => 'Number of Adults',
            ))
            ->add('children', 'choice', array(
                'choices' => $this->numbersChoices(true),
                'label' => 'Number of Children',
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
        return 'srps_bookingbundle_numberstype';
    }
}
