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
    protected $numbers;

    public function __construct($joining, $service, $numbers, $passengercount) {
        $this->joining = $joining;
        $this->service = $service;
        $this->numbers = $numbers;
        $this->passengercount = $passengercount;
    }

    private function getChoices($remaining) {

        // if the remaining meals is less than passengers then
        if ($remaining < $this->passengercount) {
            $limit = $remaining;
        } else {
            $limit = $this->passengercount;
        }

        // build array
        $choices = array(0 => 'None');
        for ($i=1; $i<=$limit; $i++) {
            $choices[$i] = "$i";
        }

        return $choices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // Make choice
        $choices = array(0 => 'None');
        for ($i=1; $i<=$this->passengercount; $i++) {
            $choices[$i] = "$i";
        }

        if ($this->joining->isMeala() and $this->service->isMealavisible()) {
            $builder
                ->add('meala', 'choice', array(
                'choices' => $this->getChoices($this->numbers->getRemainingmeala()),
                'label' => $this->service->getMealaname() . ' £' . $this->service->getMealaprice() . ' each',
            ));
        }
        if ($this->joining->isMealb() and $this->service->isMealbvisible()) {
            $builder
                ->add('mealb', 'choice', array(
                'choices' => $this->getChoices($this->numbers->getRemainingmealb()),
                'label' => $this->service->getMealbname() . ' £' . $this->service->getMealbprice() . ' each',
            ));
        }
        if ($this->joining->isMealc() and $this->service->isMealcvisible()) {
            $builder
                ->add('mealc', 'choice', array(
                'choices' => $this->getChoices($this->numbers->getRemainingmealc()),
                'label' => $this->service->getMealcname() . ' £' . $this->service->getMealcprice() . ' each',
            ));
        }
        if ($this->joining->isMeald() and $this->service->isMealdvisible()) {
            $builder
                ->add('meald', 'choice', array(
                'choices' => $this->getChoices($this->numbers->getRemainingmeald()),
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
