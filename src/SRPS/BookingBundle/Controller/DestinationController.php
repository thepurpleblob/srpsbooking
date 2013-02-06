<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Entity\Destination;
use SRPS\BookingBundle\Form\DestinationType;
use SRPS\BookingBundle\Entity\Pricebandgroup;
use SRPS\BookingBundle\Entity\Priceband;

/**
 * Service controller.
 *
 */
class DestinationController extends Controller
{
    /**
     * Lists all Service entities.
     *
     */
    public function indexAction($serviceid)
    {
        $em = $this->getDoctrine()->getManager();
        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $entities = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);

        return $this->render('SRPSBookingBundle:Destination:index.html.twig',
            array(
                'entities' => $entities,
                'service' => $service,
                'serviceid' => $serviceid
                ));
    }

    /**
     * Displays a form to create a new Destination entity.
     */
    public function newAction($serviceid)
    {
        $entity = new Destination(); 
        $entity->setServiceid($serviceid);
        
        $em = $this->getDoctrine()->getManager();        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);        
        
        $form   = $this->createForm(new DestinationType(), $entity);

        return $this->render('SRPSBookingBundle:Destination:new.html.twig', array(
            'entity' => $entity,
            'service' => $service,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * If we add a destination then we have to pad up the existing
     * pricebands
     * @param object $service service object
     */
    private function checkPricebands($service) {
        $em = $this->getDoctrine()->getManager();
        
        // Get all the destinations for this service
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($service->getId());
        
        // Get all the priceband groups for this service
        $groups = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->findByServiceid($service->getId());
        
        // run through groups checking all destinations are represented
        foreach ($groups as $group) {
            foreach ($destinations as $destination) {
                $priceband = $em->getRepository('SRPSBookingBundle:Priceband')
                    ->findOneBy(array('pricebandgroupid' => $group->getId(), 'destinationid' => $destination->getId()));
                if (!$priceband) {
                    $priceband = new Priceband();
                    $priceband->setServiceid($service->getId());
                    $priceband->setDestinationid($destination->getId());
                    $priceband->setPricebandgroupid($group->getId());
                    $em->persist($priceband);
                    $em->flush();
                }
            }
        }
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
            
            // Pad the pricebands with new destination
            $this->checkPricebands($service);

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
            
            // Check the pricebands are synced to destinations
            $this->checkPricebands($service);            

            return $this->redirect($this->generateUrl('admin_destination', array('serviceid' => $serviceid)));
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
