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
     * Edits the existing Limits entity.
     */
    public function editAction($serviceid, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');
        $logger = $this->get('logger');
        $username = $this->get('security.context')->getToken()->getUser()->getUsername();

        $limits = $em->getRepository('SRPSBookingBundle:Limits')
            ->findOneByServiceid($serviceid);

        // Get destinations (for destination limits)
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);

        // Create array of destinations limits
        $destinationlimits = array();
        foreach ($destinations as $destination) {
            $destinationlimits[$destination->getCrs()] = $destination->getBookinglimit();
        }
        $limits->setDestinationlimits($destinationlimits);

        // Get the current counts of everything
        $count = $booking->countStuff($serviceid);

        // Service
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $form = $this->createForm(new LimitsType($service), $limits);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $logger->info("$username: updating limits for " . $service->getName());
            $em->persist($limits);
            $em->flush();

            // save the destination limits
            $destinationlimits = $limits->getdestinationlimits();
            foreach ($destinationlimits as $crs=>$destinationlimit) {
                $destination = $em->getRepository('SRPSBookingBundle:Destination')
                    ->findOneBy(array('crs'=>$crs, 'serviceid'=>$serviceid));
                $destination->setBookinglimit($destinationlimit);
                $em->persist($destination);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('admin_limits_edit', array('serviceid' => $serviceid)));
        }

        return $this->render('SRPSBookingBundle:Limits:edit.html.twig', array(
            'entity' => $limits,
            'count' => $count,
            'edit_form'   => $form->createView(),
            'service' => $service,
            'serviceid' => $serviceid,
        ));
    }

}
