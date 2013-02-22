<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Form\Booking\NumbersType;
use SRPS\BookingBundle\Form\Booking\JoiningType;
use SRPS\BookingBundle\Form\Booking\DestinationType;
use Symfony\Component\Form\FormError;

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

        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // Grab current purchase
        $purchase = $booking->getPurchase($service->getId(), $code);

        // create form
        $numberstype = new NumbersType();
        $form   = $this->createForm($numberstype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check numbers
                $adults = $purchase->getAdults();
                $children = $purchase->getChildren();
                if (($adults + $children) > 16) {
                    $form->get('adults')->addError(new FormError('Total party size is more than 16.'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_joining'));
                }
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
            throw new \Exception('No joining stations found for this service');
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
        $joiningtype = new JoiningType($stations);
        $form   = $this->createForm($joiningtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check that we have a value of some sort
                if (!$purchase->getJoining()) {
                    $form->get('joining')->addError(new FormError('You must choose a joining station'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_destination'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:joining.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

    public function destinationAction()
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

        // get the destinations
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($service->getId());
        if (!$destinations) {
            throw new \Exception('No destinations found for this service');
        }

        // If there is only one then there is nothing to do
        if (count($destinations)==1) {
            $destination = array_pop($destinations);
            $purchase->setDestination($destination->getCrs());
            $em->persist($purchase);
            $em->flush();

            return $this->redirect($this->generateUrl('booking_meals'));
        }

        // we'll build up a set of data to display all the useful info in the
        // form... so bear with me
        $joiningcrs = $purchase->getJoining();
        $joining = $em->getRepository('SRPSBookingBundle:Joining')
            ->findOneBy(array('crs'=>$joiningcrs));
        $pricebandgroupid = $joining->getPricebandgroupid();
        $dests = array();
        foreach ($destinations as $destination) {
            $priceband = $em->getRepository('SRPSBookingBundle:Priceband')
                ->findOneBy(array('pricebandgroupid'=>$pricebandgroupid, 'destinationid'=>$destination->getId()));
            $dest = new \stdClass();
            $dest->crs = $destination->getCrs();
            $dest->description = $destination->getDescription();
            $dest->first = $priceband->getFirst();
            $dest->standard = $priceband->getStandard();
            $dest->child = $priceband->getChild();
            $dests[] = $dest;
        }

        // create form
        $destinationtype = new DestinationType($destinations);
        $form   = $this->createForm($destinationtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check that we have a value of some sort
                if (!$purchase->getDestination()) {
                    $form->get('destination')->addError(new FormError('You must make a choice'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_meals'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:destination.html.twig', array(
            'purchase' => $purchase,
            'destinations' => $dests,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

   public function mealsAction()
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

        // get the joining station (to see what meals available)
        $station = $em->getRepository('SRPSBookingBundle:Joining')
            ->findOneBy(array('serviceid'=>$service->getId(), 'joining'=>$purchase->getJoining()));
        if (!$station) {
            throw new \Exception('No joining stations found for this service');
        }

        // Get the passenger count
        $passengercount = $purchase->getAdults() + $purchase->getChildren();

        // create form
        $joiningtype = new JoiningType($station, $service, $passengercount);
        $form   = $this->createForm($joiningtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check that we have a value of some sort
                if (!$purchase->getJoining()) {
                    $form->get('joining')->addError(new FormError('You must choose a joining station'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_destination'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:joining.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }
}
