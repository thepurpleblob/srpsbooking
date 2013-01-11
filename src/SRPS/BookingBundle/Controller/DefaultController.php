<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SRPSBookingBundle:Default:index.html.twig');
    }
}
