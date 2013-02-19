<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JoiningType extends AbstractType
{
    protected $pricebands;

    protected $service;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // create choices array
        $choices = array();
        foreach ($this->pricebands as $priceband) {
            $choices[$priceband->getId()] = $priceband->getName();
        }
        asort($choices);
        $builder
            ->add('crs', 'text', array('label'=>'CRS'))
            ->add('station')
            ->add('pricebandgroupid', 'choice', array('choices' => $choices, 'label' => 'Priceband'));

        if ($this->service->getMealavisible()) {
            $builder
               ->add('meala', 'checkbox', array('label' => $this->service->getMealaname()));
        }
        if ($this->service->getMealbvisible()) {
            $builder
                ->add('mealb', 'checkbox', array('label' => $this->service->getMealbname()));
        }
        if ($this->service->getMealcvisible()) {
            $builder
                ->add('mealc', 'checkbox', array('label' => $this->service->getMealcname()));
        }
        if ($this->service->getMealdvisible()) {
            $builder
                ->add('meald', 'checkbox', array('label' => $this->service->getMealdname()));
        }
    }

    public function __construct($pricebands, $service) {
        $this->pricebands = $pricebands;
        $this->service = $service;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SRPS\BookingBundle\Entity\Joining'
        ));
    }

    public function getName()
    {
        return 'srps_bookingbundle_joiningtype';
    }
}
