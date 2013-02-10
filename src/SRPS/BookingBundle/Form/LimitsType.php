<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LimitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first')
            ->add('standard')
            ->add('child')
            ->add('meala', 'integer', array('label' => 'Meal A'))
            ->add('mealb', 'integer', array('label' => 'Meal B'))
            ->add('mealc', 'integer', array('label' => 'Meal C'))
            ->add('meald', 'integer', array('label' => 'Meal D'))    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SRPS\BookingBundle\Entity\Limits'
        ));
    }

    public function getName()
    {
        return 'srps_bookingbundle_limitstype';
    }
}
