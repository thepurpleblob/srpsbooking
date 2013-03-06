<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Entity\Service;
use SRPS\BookingBundle\Entity\Pricebandgroup;
use SRPS\BookingBundle\Form\ServiceType;
use SRPS\BookingBundle\Service\Booking;

/**
 * Service controller.
 *
 */
class ServiceController extends Controller
{
    /**
     * Lists all Service entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT s FROM SRPSBookingBundle:Service s ORDER BY s.date'
        );
        $entities = $query->getResult();
        
        // submitted year
        $filteryear = $this->getRequest()->request->get('filter_year');        
        
        // get possible years and filter results
        // shouldn't have to do this in PHP but Doctrine sucks badly!
        $services = array();
        $years = array();
        $years['All'] = 'All';        
        foreach ($entities as $service) {
            $servicedate = $service->getDate();
            $year = $servicedate->format('Y');
            $years[$year] = $year;
            if ($filteryear=='All' or $filteryear=='') {
                $services[] = $service;
            } else if ($year == $filteryear) {
                $services[] = $service;
            }
        }


        // get booking status
        $enablebooking = $this->container->getParameter('enablebooking');

        return $this->render('SRPSBookingBundle:Service:index.html.twig',
            array('entities' => $services,
                  'enablebooking' => $enablebooking,
                  'years' => $years,
                  'filteryear' => $filteryear,
                ));
    }

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
     * Finds and displays a Service entity.
     *
     * @Route("/{id}/show", name="admin_service_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SRPSBookingBundle:Service')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }

        // Get the other information stored for this service
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($id);
        $pricebandgroups = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
            ->findByServiceid($id);
        $joinings = $em->getRepository('SRPSBookingBundle:Joining')
            ->findByServiceid($id);

        // iterate over these and get destinations
        // (very inefficiently)
        $booking = $this->get('srps_booking');
        foreach ($pricebandgroups as $band) {
            $pricebandgroupid = $band->getId();
            $bandtable = $booking->createPricebandTable($pricebandgroupid);
            $band->bandtable = $bandtable;
        }

        // add pricebandgroup names
        foreach ($joinings as $joining) {
            $pricebandgroup = $em->getRepository('SRPSBookingBundle:Pricebandgroup')
                ->find($joining->getPricebandgroupid());
            $joining->setPricebandname($pricebandgroup->getName());
        }

        return $this->render('SRPSBookingBundle:Service:show.html.twig', array(
            'entity' => $entity,
            'destinations' => $destinations,
            'pricebandgroups' => $pricebandgroups,
            'joinings' => $joinings,
            'serviceid' => $id,
        ));
    }

    /**
     * Displays a form to create a new Service entity.
     */
    public function newAction()
    {
        $entity = new Service();

        $form   = $this->createForm(new ServiceType(), $entity);

        return $this->render('SRPSBookingBundle:Service:new.html.twig', array(
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
        $form = $this->createForm(new ServiceType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_service_show', array('id' => $entity->getId())));
        }

        return $this->render('SRPSBookingBundle:Service:new.html.twig', array(
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

        $entity = $em->getRepository('SRPSBookingBundle:Service')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }

        $editForm = $this->createForm(new ServiceType(), $entity);

        return $this->render('SRPSBookingBundle:Service:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'serviceid' => $id,
        ));
    }

    /**
     * Edits an existing Service entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SRPSBookingBundle:Service')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }

        $editForm = $this->createForm(new ServiceType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_service_show', array('id' => $id)));
        }

        return $this->render('SRPSBookingBundle:Service:edit.html.twig', array(
            'entity'      => $entity,
            //'destinations' => $destinations,
            'edit_form'   => $editForm->createView(),
            'serviceid' => $id,
        ));
    }

    /**
     * Calls routines to set the system up
     * (hidden)
     */
    public function installAction() {

        // Install the list of crs codes and stations
        $stations = $this->get('srps_stations');

        if ($stations->installStations()) {
            return new Response("<p>The Stations list was installed</p>");
        }
        else {
            return new Response("<p>The Stations list is already populated. No action taken</p>");
        }

    }
}
