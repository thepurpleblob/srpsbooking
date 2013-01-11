<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('description')
            ->add('visible')
            ->add('date')
            ->add('mealaname')
            ->add('mealavisible')
            ->add('mealaprice')
            ->add('mealbname')
            ->add('mealbvisible')
            ->add('mealbprice')
            ->add('mealcname')
            ->add('mealcvisible')
            ->add('mealcprice')
            ->add('mealdname')
            ->add('mealdvisible')
            ->add('mealdprice')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SRPS\BookingBundle\Entity\Service'
        ));
    }

    public function getName()
    {
        return 'srps_bookingbundle_servicetype';
    }
}
