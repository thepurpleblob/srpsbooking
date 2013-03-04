<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdditionalType extends AbstractType
{

    protected $iscomments;
    
    protected $issupplement;
    
    public function __construct($iscomments, $issupplement) {
        $this->iscomments = $iscomments;
        $this->issupplement = $issupplement;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // choices
        $yesno = array(
            0 => 'No',
            1 => 'Yes'
        );
        
        if ($this->iscomments) {
            $builder->add('comment', 'text', array(
                'max_length' => 39,
                'label' => 'Your comment',
            ));
        }
        if ($this->issupplement) {
            $builder->add('seatsupplement', 'choice', array(
                'choices' => $yesno,
                'label' => 'Single windows seats required',
            ));
        }
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
        return 'srps_bookingbundle_booking_additionaltype';
    }
}
