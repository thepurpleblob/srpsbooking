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
            ->add('visible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))
            ->add('date', 'date', array('widget'=>'single_text'))
            ->add('mealaname')
            ->add('mealavisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))                
            ->add('mealaprice')
            ->add('mealbname')
            ->add('mealbvisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))                
            ->add('mealbprice')
            ->add('mealcname')
            ->add('mealcvisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))                
            ->add('mealcprice')
            ->add('mealdname')
            ->add('mealdvisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))              
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
