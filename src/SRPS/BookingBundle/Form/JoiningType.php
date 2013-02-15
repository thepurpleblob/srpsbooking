<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JoiningType extends AbstractType
{
    protected $pricebands;

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
            ->add('pricebandgroupid', 'choice', array('choices' => $choices, 'label' => 'Priceband'))
        ;
    }

    public function __construct($pricebands) {
        $this->pricebands = $pricebands;
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
