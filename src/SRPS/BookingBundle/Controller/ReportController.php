<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function listAction($serviceid)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Clear session and delete expired purchases
        $booking->cleanPurchases();

        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);
        if (!$service) {
            throw new \Exception('Unable to find service');
        }

        // get the purchases for this service
        $purchases = $em->getRepository('SRPSBookingBundle:Purchase')
            ->findBy(array('serviceid'=>$serviceid, 'completed'=>true));

        return $this->render('SRPSBookingBundle:Report:list.html.twig', array(
            'service' => $service,
            'purchases' => $purchases,
        ));
    }

    public function viewAction($purchaseid)
    {
        $em = $this->getDoctrine()->getManager();

        // Get the purchase record
        $purchase = $em->getRepository('SRPSBookingBundle:Purchase')
            ->find($purchaseid);
        if (!$purchase) {
            throw new \Exception('purchase item could not be found');
        }

        // Get the service object
        $serviceid = $purchase->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);
        if (!$service) {
            throw new \Exception('Unable to find service');
        }

        return $this->render('SRPSBookingBundle:Report:view.html.twig', array(
            'service' => $service,
            'purchase' => $purchase,
        ));
    }
    
    public function exportAction($serviceid) {
        $em = $this->getDoctrine()->getManager();
        $reports = $this->get('srps_reports');
        
        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);
        if (!$service) {
            throw new \Exception('Unable to find service');
        }   
        
        // Get the purchases
        $purchases = $em->getRepository('SRPSBookingBundle:Purchase')
            ->findBy(array(
                'serviceid' => $serviceid,
                'completed' => true,
                'status' => 'OK',
                ));
        
        // if there are none, then nothing to do
        if (!$purchases) {
            return $this->redirect($this->generateUrl('admin'));
        }
        
        // Create a filename
        $filename = "rt-bkg-".$service->getCode().'.csv';
        
        // create the response
        $response = new Response();
        $response->setContent($reports->getExport($purchases));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"'); 
        
        return $response;
    }
}

