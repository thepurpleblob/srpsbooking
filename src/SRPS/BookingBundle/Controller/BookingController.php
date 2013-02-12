<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }
    }

}
