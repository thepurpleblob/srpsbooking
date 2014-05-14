<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use SRPS\BookingBundle\Entity\Priceband;
use SRPS\BookingBundle\Entity\Pricebandgroup;
use SRPS\BookingBundle\Form\PricebandType;
use SRPS\BookingBundle\Form\PricebandgroupType;

/**
 * Service controller.
 *
 */
class PricebandController extends Controller
{
    
    /**
     * Lists all Priceband entities.
     *
     */
    public function indexAction($serviceid)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');
        
        // Get the Service entity
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);
        
        // Get the Pricebandgroup entities
        $pricebandgroup = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->findByServiceid($serviceid);
        
        // Get destinations mostly to check that there are some
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);
        
        // Get the band info to go with bands
        foreach ($pricebandgroup as $band) {
            $pricebandgroupid = $band->getId();
            $bandtable = $booking->createPricebandTable($pricebandgroupid);
            $band->bandtable = $bandtable;
            $band->setUsed($booking->isPricebandUsed($band));
        }

        return $this->render('SRPSBookingBundle:Priceband:index.html.twig',
            array(
                'bands' => $pricebandgroup,
                'destinations' => $destinations,
                'service' => $service,
                'serviceid' => $serviceid
                ));
    }

    /**
     * Displays a form to create a new Priceband entity.
     */
    public function newAction($serviceid)
    {
        // Get service entity
        $em = $this->getDoctrine()->getManager();        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid); 
        
        // Get destinations for this service
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);
        
        // create empty priceband entities
        $pricebandgroup = new Pricebandgroup();
        $pricebandgroup->setServiceid($serviceid);
        foreach ($destinations as $destination) {
            $priceband = new Priceband();
            $priceband->setServiceid($serviceid);
            $priceband->setDestinationid($destination->getId());
            $priceband->setDestination($destination->getName());
            $pricebandgroup->getPricebands()->add($priceband);
        }
     
        $form   = $this->createForm(new PricebandgroupType(), $pricebandgroup);

        return $this->render('SRPSBookingBundle:Priceband:edit.html.twig', array(
            'pricebands' => $pricebandgroup,
            'service' => $service,
            'destinations' => $destinations,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Edits an existing Priceband.
     */
    public function editAction($serviceid, $pricebandgroupid, Request $request)
    {
        $em = $this->getDoctrine()->getManager();  
        if ($pricebandgroupid) {
            $pricebandgroup = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
                ->find($pricebandgroupid);
        } else {
            $pricebandgroup = new Pricebandgroup;
        }    
        $pricebandgroup->setServiceid($serviceid);
        $pricebandgroup->setPricebands(new ArrayCollection());

        // Get the service 
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid); 
        
        // Get destinations for this service
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);
        
        // get/create priceband entities
        // TODO: Won't work for new at the moment
        $pricebandgroup->setServiceid($serviceid);   
        foreach ($destinations as $destination) {
            $priceband = $em->getRepository('SRPSBookingBundle:Priceband')
                ->findOneBy(array('pricebandgroupid'=>$pricebandgroupid, 'destinationid'=>$destination->getId()));
            if (!$priceband) {
                $priceband = new Priceband;
                $priceband->setServiceid($serviceid);
                $priceband->setDestinationid($destination->getId());
                $priceband->setPricebandgroupid($pricebandgroupid);
            }
            $priceband->setDestination($destination->getName());
            $pricebandgroup->getPricebands()->add($priceband);
        }
//echo "<pre>"; var_dump($pricebandgroup); die;
        
        $form = $this->createForm(new PricebandgroupType(), $pricebandgroup);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($pricebandgroup);
            $em->flush();
            $pricebandgroupid = $pricebandgroup->getId();
            $pricebands = $pricebandgroup->getPricebands();
            foreach ($pricebands as $priceband) {
                $priceband->setPricebandgroupid($pricebandgroupid);
                $em->persist($priceband);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('admin_priceband', array('serviceid' => $serviceid)));
        }

        return $this->render('SRPSBookingBundle:Priceband:edit.html.twig', array(
            'pricebands' => $pricebandgroup,
            'service' => $service,
            'destinations' => $destinations,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));  
    }

    /**
     * Deletes a Service entity.
     *
     */
    public function deleteAction($pricebandgroupid)
    {

        $em = $this->getDoctrine()->getManager();
        
        // Remove pricebands associated with this group
        $pricebands = $em->getRepository('SRPSBookingBundle:Priceband')
            ->findByPricebandgroupid($pricebandgroupid);
        if ($pricebands) {
            foreach ($pricebands as $priceband)  {
                $em->remove($priceband);
            }  
            $em->flush();
        }    
        
        // Remove pricebandgroup
        $pricebandgroup = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->find($pricebandgroupid);
        if ($pricebandgroup) {
            $serviceid = $pricebandgroup->getServiceid();
            $em->remove($pricebandgroup);
            $em->flush();
            
            return $this->redirect($this->generateUrl('admin_priceband', array('serviceid' => $serviceid)));          
        }

        return $this->redirect($this->generateUrl('admin_service'));
    }
}
