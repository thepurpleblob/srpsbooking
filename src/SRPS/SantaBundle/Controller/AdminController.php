<?php

namespace SRPS\SantaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function IndexAction()
    {
        return $this->render('SRPSSantaBundle:Admin:index.html.twig');
    }
    
    public function TimetableAction()
    {
        return $this->render('SRPSSantaBundle:admin:timetable.html.twig');
    }

}
