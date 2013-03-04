<?php

namespace SRPS\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        // Choices for maxparty
        $mpchoices = array();
        for ($i=1; $i<=20; $i++) {
            $mpchoices[$i] = $i;
        }   
        
        // Choices for yes/no
        $yesno = array(0 => 'No', '1' => 'Yes');
        
        
        $builder
            ->add('code')
            ->add('name')
            ->add('description')
            ->add('visible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))
            ->add('date', 'date', array('widget'=>'single_text', 'format'=>'dd/MM/yyyy'))
            ->add('maxparty', 'choice', array(
                'label' => "Max party that may be booked",
                'choices' => $mpchoices,
            ))  
            ->add('singlesupplement', 'money', array(
                'label' => 'First window/single supplement',
                'currency' => 'GBP',
            ))
            ->add('commentbox', 'choice', array(
                'label' => 'Enable comment field',
                'choices' => $yesno,
            ))    
            ->add('mealaname')
            ->add('mealavisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))
            ->add('mealaprice', 'money', array(
                'currency' => 'GBP',
            ))
            ->add('mealbname')
            ->add('mealbvisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))
            ->add('mealbprice', 'money', array(
                'currency' => 'GBP',
            ))
            ->add('mealcname')
            ->add('mealcvisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))
            ->add('mealcprice', 'money', array(
                'currency' => 'GBP',
            ))
            ->add('mealdname')
            ->add('mealdvisible', 'choice', array(
                'choices' => array(1 => 'Yes', 0 => 'No'),
                'required' => false
            ))
            ->add('mealdprice', 'money', array(
                'currency' => 'GBP',
            ))
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
