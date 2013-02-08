<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Entity\Destination;
use SRPS\BookingBundle\Form\DestinationType;
use SRPS\BookingBundle\Form\JoiningType;
use SRPS\BookingBundle\Entity\Pricebandgroup;
use SRPS\BookingBundle\Entity\Priceband;
use SRPS\BookingBundle\Entity\Joining;

/**
 * Joining controller.
 *
 */
class JoiningController extends Controller
{
    /**
     * Lists all Joining entities.
     *
     */
    public function indexAction($serviceid)
    {
        $em = $this->getDoctrine()->getManager();
        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $entities = $em->getRepository('SRPSBookingBundle:Joining')
            ->findByServiceid($serviceid);
        
        $pricebands = $em->getRepository('SRPSBookingBundle:Priceband')
            ->findByServiceid($serviceid);
        
        // add pricebandgroup names
        foreach ($entities as $entity) {
            $pricebandgroup = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
                ->find($entity->getPricebandgroupid());
            $entity->setPricebandname($pricebandgroup->getName());
        }        
        
        // We just want to know if the pricebands are set up
        $setup = !empty($pricebands);

        return $this->render('SRPSBookingBundle:Joining:index.html.twig',
            array(
                'entities' => $entities,
                'service' => $service,
                'serviceid' => $serviceid,
                'setup' => $setup
                ));
    }

    /**
     * Displays a form to create a new Destination entity.
     */
    public function newAction($serviceid)
    {
        $entity = new Joining(); 
        $entity->setServiceid($serviceid);
        
        $em = $this->getDoctrine()->getManager();        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);      
        
        $pricebandgroups = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->findByServiceid($serviceid);
        
        $joiningtype = new JoiningType($pricebandgroups);
        $form   = $this->createForm($joiningtype, $entity);

        return $this->render('SRPSBookingBundle:Joining:new.html.twig', array(
            'entity' => $entity,
            'service' => $service,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Destination entity.
     */
    public function createAction(Request $request, $serviceid)
    {
        $entity = new Joining();
        $entity->setServiceid($serviceid);
        
        $em = $this->getDoctrine()->getManager();        
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);   
        
        $pricebandgroups = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->findByServiceid($serviceid);        
        
        $joiningtype = new JoiningType($pricebandgroups);        
        $form = $this->createForm($joiningtype, $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_joining', array('serviceid' => $serviceid)));
        }

        return $this->render('SRPSBookingBundle:Joining:new.html.twig', array(
            'entity' => $entity,
            'service' => $service,
            'serviceid' => $serviceid,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Destination entity.
     */
    public function editAction($joiningid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SRPSBookingBundle:Joining')
            ->find($joiningid);

        // Service
        $serviceid = $entity->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);  
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destination entity.');
        }
        
        $pricebandgroups = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->findByServiceid($serviceid);
        
        $joiningtype = new JoiningType($pricebandgroups);        
        $editForm = $this->createForm($joiningtype, $entity);

        return $this->render('SRPSBookingBundle:Joining:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'service' => $service,
            'serviceid' => $serviceid,
        ));
    }

    /**
     * Edits an existing Destination entity.
     */
    public function updateAction(Request $request, $joiningid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SRPSBookingBundle:Joining')
            ->find($joiningid);
        
        // Service
        $serviceid = $entity->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);  
                
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destination entity.');
        }
        
        $pricebandgroups = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->findByServiceid($serviceid);
        
        $joiningtype = new JoiningType($pricebandgroups);        
        $editForm = $this->createForm($joiningtype, $entity);        
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_joining', array('serviceid' => $serviceid)));
        }

        return $this->render('SRPSBookingBundle:Destination:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
