<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Form\NumbersType;

class BookingController extends Controller
{
    public function indexAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        return $this->render('SRPSBookingBundle:Booking:index.html.twig', array(
            'code' => $code,
            'service' => $service
        ));
    }

    public function numbersAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase($code);

        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // create form
        $numberstype = new NumbersType();
        $form   = $this->createForm($numberstype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em->persist($purchase);
                $em->flush();

                //return $this->redirect($this->generateUrl('', array('serviceid' => $serviceid)));
                die;
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:numbers.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

}
