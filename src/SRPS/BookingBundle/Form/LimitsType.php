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

        // Choices for maxparty
        $mpchoices = array();
        for ($i=1; $i<=20; $i++) {
            $mpchoices[$i] = $i;
        }
        $mfchoices = $mpchoices;
        $mfchoices[0] = 0;

        $builder
            ->add('first')
            ->add('standard')
            ->add('firstsingles', 'integer', array(
                'label' => 'Single/Window seats in First',
            ))
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
            ->add('maxparty', 'choice', array(
                'label' => "Max party that may be booked",
                'choices' => $mpchoices,
            ))
            ->add('maxpartyfirst', 'choice', array(
                'label' => "If nonzero, max party in first that may be booked",
                'choices' => $mfchoices,
            ))
            ->add('destinationlimits', 'collection', array(
            	'type' => 'integer',
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
