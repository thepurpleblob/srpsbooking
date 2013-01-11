<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('SRPSBookingBundle:Admin:index.html.twig');
    }
}
