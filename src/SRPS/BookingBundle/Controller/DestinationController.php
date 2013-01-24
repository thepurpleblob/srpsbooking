<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Entity\Destination;
use SRPS\BookingBundle\Form\DestinationType;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SRPSBookingBundle:Destination')->findAll();

        return $this->render('SRPSBookingBundle:Destination:index.html.twig',
            array('entities'=>$entities));
    }

    /**
     * Finds and displays a Service entity.
     *
     * @Route("/{id}/show", name="admin_service_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SRPSBookingBundle:Destination')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destination entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SRPSBookingBundle:Destination:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Service entity.
     */
    public function newAction()
    {
        $entity = new Service();       
        
        $form   = $this->createForm(new DestinationType(), $entity);

        return $this->render('SRPSBookingBundle:Destination:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Service entity.
     */
    public function createAction(Request $request)
    {
        $entity  = new Service();
        $form = $this->createForm(new DestinationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_destination_show', array('id' => $entity->getId())));
        }

        return $this->render('SRPSBookingBundle:Destination:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Service entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SRPSBookingBundle:Destination')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destination entity.');
        }

        $editForm = $this->createForm(new DestinationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SRPSBookingBundle:Destination:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Service entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SRPSBookingBundle:Destination')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destination entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DestinationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_destination_show', array('id' => $id)));
        }

        return $this->render('SRPSBookingBundle:Destination:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
