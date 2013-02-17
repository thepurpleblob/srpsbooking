<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Form\Booking\NumbersType;

class BookingController extends Controller
{
    public function indexAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Clear session and delete expired purchases
        $booking->cleanPurchases();

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

                return $this->redirect($this->generateUrl('booking_joining'));
            }
echo "<pre>ERR  "; print_r($form->getErrors()); die;
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:numbers.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

    public function joiningAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // get the joining stations
        $stations = $em->getRepository('SRPSBookingBundle:Joining')
            ->findByServiceid($service->getId());
        if (!$stations) {
            throw new Exception('No joining stations found for this service');
        }

        // If there is only one then there is nothing to do
        if (count($stations)==1) {
            $station = array_pop($stations);
            $purchase->setJoining($station->getCrs());
            $em->persist($purchase);
            $em->flush();

            return $this->redirect($this->generateUrl('booking_destination'));
        }

        // create form
        $joiningtype = new JoiningType();
        $form   = $this->createForm($joiningtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em->persist($purchase);
                $em->flush();

echo "HERE"; die;
                return $this->redirect($this->generateUrl('booking_destination'));
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
