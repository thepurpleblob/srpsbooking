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
    
    private function getOptions($remaining, $name, $price, $joining, $station) {
        $options = array();
        $options['choices'] = $this->getChoices($remaining);
        if (!$joining) {
            $options['label'] = "$name not from $station";
            $options['disabled'] = true;
        } else if ($remaining) {
            $options['label'] = "$name £$price each";
        } else {
            $options['label'] = "$name fully booked";
            $options['disabled'] = true;
        }
        return $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if ($this->service->isMealavisible()) {
            $builder
                ->add('meala', 'choice', $this->getOptions(
                    $this->numbers->getRemainingmeala(),
                    $this->service->getMealaname(),
                    $this->service->getMealaprice(),
                    $this->joining->isMeala(),
                    $this->joining->getStation()
                    )
            );
        }
        if ($this->service->isMealbvisible()) {
            $builder
                ->add('mealb', 'choice', $this->getOptions(
                    $this->numbers->getRemainingmealb(),
                    $this->service->getMealbname(),
                    $this->service->getMealbprice(),
                    $this->joining->isMealb(),
                    $this->joining->getStation()                        
                    )
            );
        }
        if ($this->service->isMealcvisible()) {
            $builder
                ->add('mealc', 'choice', $this->getOptions(
                    $this->numbers->getRemainingmealc(),
                    $this->service->getMealcname(),
                    $this->service->getMealcprice(),
                    $this->joining->isMealc(),
                    $this->joining->getStation()                        
                    )
            );
        }
        if ($this->service->isMealdvisible()) {
            $builder
                ->add('meald', 'choice', $this->getOptions(
                    $this->numbers->getRemainingmeald(),
                    $this->service->getMealdname(),
                    $this->service->getMealdprice(),
                    $this->joining->isMeald(),
                    $this->joining->getStation()                        
                    )
            );
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
