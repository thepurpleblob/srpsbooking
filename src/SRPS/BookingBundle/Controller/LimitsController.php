<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Entity\Limits;
use SRPS\BookingBundle\Form\LimitsType;

/**
 * Limits controller.
 *
 */
class LimitsController extends Controller
{

    /**
     * Displays a form to edit the Limits entity.
     */
    public function editAction($serviceid)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Always a chance the entity doesn't exist yet
        $entity = $em->getRepository('SRPSBookingBundle:Limits')
            ->findOneByServiceid($serviceid);
        if (!$entity) {
            $entity = new Limits();
            $entity->setServiceid($serviceid);
            $em->persist($entity);
            $em->flush();
        }

        // Get the current counts of everything
        $count = $booking->countStuff($serviceid);

        // Service
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $editForm = $this->createForm(new LimitsType($service), $entity);

        return $this->render('SRPSBookingBundle:Limits:edit.html.twig', array(
            'entity'      => $entity,
            'count' => $count,
            'edit_form'   => $editForm->createView(),
            'service' => $service,
            'serviceid' => $serviceid,
        ));
    }

    /**
     * Edits the existing Limits entity.
     */
    public function updateAction(Request $request, $serviceid)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');
        $logger = $this->get('logger');
        $username = $this->get('security.context')->getToken()->getUser()->getUsername();        

        $entity = $em->getRepository('SRPSBookingBundle:Limits')
            ->findOneByServiceid($serviceid);
        if (!$entity) {
            $entity = new Limits();
            $entity->setServiceid($serviceid);
        }

        // Service
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $editForm = $this->createForm(new LimitsType($service), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $logger->info("$username: updating limits for " . $service->getName());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_limits_edit', array('serviceid' => $serviceid)));
        }

        return $this->render('SRPSBookingBundle:Limits:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'service' => $service,
            'serviceid' => $serviceid,
        ));
    }

}
