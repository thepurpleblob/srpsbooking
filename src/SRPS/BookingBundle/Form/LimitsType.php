<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LimitsType extends AbstractType
{
    protected $service;

    public function __construct($service) {
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first')
            ->add('standard')
            ->add('meala', 'integer', array(
                'label' => $this->service->getMealaname(),
                'disabled' => !$this->service->isMealavisible(),
                ))
            ->add('mealb', 'integer', array(
                'label' => $this->service->getMealbname(),
                'disabled' => !$this->service->isMealbvisible(),
                ))
            ->add('mealc', 'integer', array(
                'label' => $this->service->getMealcname(),
                'disabled' => !$this->service->isMealcvisible(),
                ))
            ->add('meald', 'integer', array(
                'label' => $this->service->getMealdname(),
                'disabled' => !$this->service->isMealdvisible(),
                ))
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
