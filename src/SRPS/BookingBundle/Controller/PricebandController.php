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
     * Create the table to display price band group
     * @param integer $pricebandgroupid
     */
    private function createPricebandTable($pricebandgroupid) {
        $em = $this->getDoctrine()->getManager();
        
        // get the basic price bands
        $pricebands = $em->getRepository('SRPSBookingBundle:Priceband')
            ->findByPricebandgroupid($pricebandgroupid);
        
        // iterate over these and get destinations 
        // (very inefficiently)
        foreach ($pricebands as $priceband) {
            $destinationid = $priceband->getDestinationid();
            $destination = $em->getRepository('SRPSBookingBundle:Destination')
                ->find($destinationid);
            $priceband->setDestination($destination->getName());
        }
        
        return $pricebands;
    }
    
    /**
     * Lists all Priceband entities.
     *
     */
    public function indexAction($serviceid)
    {
        $em = $this->getDoctrine()->getManager();
        
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
            $bandtable = $this->createPricebandTable($pricebandgroupid);
            $band->bandtable = $bandtable;
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

        return $this->render('SRPSBookingBundle:Priceband:new.html.twig', array(
            'pricebands' => $pricebandgroup,
            'service' => $service,
            'destinations' => $destinations,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Destination entity.
     */
    public function createAction(Request $request, $serviceid)
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
        
        $form = $this->createForm(new PricebandgroupType(), $pricebandgroup);
        $form->bind($request);

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

        return $this->render('SRPSBookingBundle:Priceband:new.html.twig', array(
            'pricebands' => $pricebandgroup,
            'service' => $service,
            'destinations' => $destinations,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Priceband entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pricebandgroup = $em->getRepository('SRPSBookingBundle:Pricebandgroup')->find($id); 
        
        // remember _constructor isn't called
        $pricebandgroup->setPricebands(new ArrayCollection());

        // Service
        $serviceid = $pricebandgroup->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);  
        
        // Get destinations for this service
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);
        
        // get priceband for destination(s)
        foreach ($destinations as $destination) {
            $priceband = $em->getRepository('SRPSBookingBundle:Priceband')
                ->findOneBy(array('pricebandgroupid'=>$id, 'destinationid'=>$destination->getId()));
            
            // It's possible destination has been added
            if (!$priceband) {
                $priceband = new Priceband();
                $priceband->setPricebandgroupid($id);
                $priceband->setServiceid($serviceid);
                $priceband->setDestinationid($destination->getId());               
            }
            $priceband->setDestination($destination->getName());
            $pricebandgroup->getPricebands()->add($priceband);
        }

        $editForm = $this->createForm(new PricebandgroupType(), $pricebandgroup);

        return $this->render('SRPSBookingBundle:Priceband:edit.html.twig', array(
            'pricebands' => $pricebandgroup,
            'service' => $service,
            'destinations' => $destinations,
            'serviceid' => $serviceid,
            'form'   => $editForm->createView(),
        ));        
    }

    /**
     * Edits an existing Destination entity.
     */
    public function updateAction(Request $request, $pricebandgroupid)
    {
        $em = $this->getDoctrine()->getManager();    
        $pricebandgroup = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->find($pricebandgroupid);
        $pricebandgroup->setPricebands(new ArrayCollection());
        
        // Get the service 
        $serviceid = $pricebandgroup->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid); 
        
        // Get destinations for this service
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);
        
        // create empty priceband entities
        $pricebandgroup->setServiceid($serviceid);   
        foreach ($destinations as $destination) {
            $priceband = $em->getRepository('SRPSBookingBundle:Priceband')
                ->findOneBy(array('pricebandgroupid'=>$pricebandgroupid, 'destinationid'=>$destination->getId()));
            $pricebandgroup->getPricebands()->add($priceband);
        }        
        
        $form = $this->createForm(new PricebandgroupType(), $pricebandgroup);
        $form->bind($request);

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
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SRPSBookingBundle:Service')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Service entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_service'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
