<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Entity\Priceband;
use SRPS\BookingBundle\Entity\Pricebands;
use SRPS\BookingBundle\Form\PricebandType;
use SRPS\BookingBundle\Form\PricebandsType;

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
        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $entities = $em->getRepository('SRPSBookingBundle:Priceband')
            ->findByServiceid($serviceid);

        return $this->render('SRPSBookingBundle:Priceband:index.html.twig',
            array(
                'entities' => $entities,
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
        $pricebands = new Pricebands();
        foreach ($destinations as $destination) {
            $priceband = new Priceband();
            $priceband->setServiceid($serviceid);
            $priceband->setDestinationid($destination->getId());
            $pricebands->getPricebands()->add($priceband);
        }
     
        $form   = $this->createForm(new PricebandsType(), $pricebands);

        return $this->render('SRPSBookingBundle:Priceband:new.html.twig', array(
            'pricebands' => $pricebands,
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
        $entity = new Destination();
        $entity->setServiceid($serviceid);
        
        $em = $this->getDoctrine()->getManager();        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);         
        
        $form = $this->createForm(new DestinationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_destination', array('serviceid' => $serviceid)));
        }

        return $this->render('SRPSBookingBundle:Destination:new.html.twig', array(
            'entity' => $entity,
            'service' => $service,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Destination entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SRPSBookingBundle:Destination')->find($id);

        // Service
        $serviceid = $entity->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);  
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destination entity.');
        }

        $editForm = $this->createForm(new DestinationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SRPSBookingBundle:Destination:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'service' => $service,
            'serviceid' => $serviceid,
        ));
    }

    /**
     * Edits an existing Destination entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SRPSBookingBundle:Destination')->find($id);
        
        // Service
        $serviceid = $entity->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);  
                
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destination entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DestinationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_destination', array('serviceid' => $id)));
        }

        return $this->render('SRPSBookingBundle:Destination:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'service' => $service,
            'serviceid' => $serviceid,            
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
