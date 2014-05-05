<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Entity\Destination;
use SRPS\BookingBundle\Form\DestinationType;
use SRPS\BookingBundle\Entity\Pricebandgroup;
use SRPS\BookingBundle\Entity\Priceband;

/**
 * Destination controller.
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
        $booking = $this->get('srps_booking');

        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $entities = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($serviceid);

        // Check if used
        foreach ($entities as $entity) {
            $entity->setUsed( $booking->isDestinationUsed($entity));
        }

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
        $destination = new Destination();
        $destination->setServiceid($serviceid);
        
        // Tell edit form this is a new entry
        $destination->setId(0);

        $em = $this->getDoctrine()->getManager();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);

        $form = $this->createForm(new DestinationType(), $destination);

        return $this->render('SRPSBookingBundle:Destination:edit.html.twig', array(
            'destination' => $destination,
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
     * If CRS is populated and name is not, try looking
     * up name
     * @param string $dcrs
     */
    private function findCrs($crs, $name) {
        $em = $this->getDoctrine()->getManager();

        if (empty($name) and !empty($crs)) {
            $station = $em->getRepository('SRPSBookingBundle:Station')
                ->findOneByCrs($crs);
            if ($station) {
                return $station->getName();
            }
        }

        return $name;
    }

    /**
     * Edits an existing Destination entity.
     */
    public function editAction($serviceid, $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $destination = $em->getRepository('SRPSBookingBundle:Destination')->find($id);
            if (!$destination) {
                throw $this->createNotFoundException('Unable to find Destination.');
            }
        } else {
            $destination = new Destination();
        }
        
        // Service
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);
        $destination->setServiceid($serviceid);

        $form = $this->createForm(new DestinationType(), $destination);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $destination->setCrs(strtoupper($destination->getCrs()));
            $em->persist($destination);
            $em->flush();

            // Check the pricebands are synced to destinations
            $this->checkPricebands($service);

            return $this->redirect($this->generateUrl('admin_destination', array('serviceid' => $serviceid)));
        }

        return $this->render('SRPSBookingBundle:Destination:edit.html.twig', array(
            'destination' => $destination,
            'form' => $form->createView(),
            'service' => $service,
            'serviceid' => $serviceid,
        ));
    }

    /**
     * Deletes a Service entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // delete pricebands associated with this
        $pricebands = $em->getRepository('SRPSBookingBundle:Priceband')
            ->findByDestinationid($id);
        if ($pricebands) {
            foreach ($pricebands as $priceband) {
                $em->remove($priceband);
            }
        }
        $em->flush();

        // delete destination
        $destination = $em->getRepository('SRPSBookingBundle:Destination')
            ->find($id);
        if ($destination) {
            $serviceid = $destination->getServiceid();
            $em->remove($destination);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_destination', array('serviceid' => $serviceid)));
        }

        return $this->redirect($this->generateUrl('admin_service'));
    }

    /**
     * Ajax function to find name from crs
     */
    public function ajaxAction() {
       $em = $this->getDoctrine()->getManager();

       // Get post variable for CRS
       $crs = $_POST['crstyped'];

       // Attempt to find in db
       $station = $em->getRepository('SRPSBookingBundle:Station')
           ->findOneByCrs($crs);
       if ($station) {
           return new Response($station->getName());
       } else {
           return new Response('');
       }
    }
}
