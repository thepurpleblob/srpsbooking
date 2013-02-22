<?php

namespace SRPS\BookingBundle\Form\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MealsType extends AbstractType
{

    protected $joining;
    protected $passengercount;
    protected $service;

    public function __construct($joining, $service, $passengercount) {
        $this->joining = $joining;
        $this->service = $service;
        $this->passengercount = $passengercount;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // Make choice
        $choices = array(0 => 'None');
        for ($i=1; $i<=$this->passengercount; $i++) {
            $choices[$i] = "$i";
        }

        if ($this->joining->isMeala()) {
            $builder
                ->add('meala', 'choice', array(
                'choices' => $choices,
                'label' => $this->service->getMealaname() . ' £' . $this->service->getMealaprice() . ' each',
            ));
        }
        if ($this->joining->isMealb()) {
            $builder
                ->add('mealb', 'choice', array(
                'choices' => $choices,
                'label' => $this->service->getMealbname() . ' £' . $this->service->getMealbprice() . ' each',
            ));
        }
        if ($this->joining->isMealc()) {
            $builder
                ->add('mealc', 'choice', array(
                'choices' => $choices,
                'label' => $this->service->getMealcname() . ' £' . $this->service->getMealcprice() . ' each',
            ));
        }
        if ($this->joining->isMeald()) {
            $builder
                ->add('meald', 'choice', array(
                'choices' => $choices,
                'label' => $this->service->getMealdname() . ' £' . $this->service->getMealdprice() . ' each',
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
        return 'srps_bookingbundle_booking_mealstype';
    }
}
