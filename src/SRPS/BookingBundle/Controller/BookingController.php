<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SRPS\BookingBundle\Form\Booking\NumbersType;
use SRPS\BookingBundle\Form\Booking\JoiningType;
use SRPS\BookingBundle\Form\Booking\DestinationType;
use SRPS\BookingBundle\Form\Booking\MealsType;
use SRPS\BookingBundle\Form\Booking\ClassType;
use SRPS\BookingBundle\Form\Booking\AdditionalType;
use SRPS\BookingBundle\Form\Booking\PersonalType;
use Symfony\Component\Form\FormError;

class BookingController extends Controller
{
    public function indexAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Clear session and delete expired purchases
        $booking->cleanPurchases();

        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // count the seats left
        $count = $booking->countStuff($service->getId());

        // decide if we can go ahead with the booking
        $today = new \DateTime('today midnight');
        $seatsavailable =
            (($count->getRemainingfirst()>0) or ($count->getRemainingstandard()>0));
        $isvisible = ($service->isVisible());
        $isindate = ($service->getDate() > $today);

        if ($seatsavailable and $isvisible and $isindate) {
            return $this->render('SRPSBookingBundle:Booking:index.html.twig', array(
                'code' => $code,
                'service' => $service
            ));
        } else {
             return $this->render('SRPSBookingBundle:Booking:closed.html.twig', array(
                'code' => $code,
                'service' => $service
            ));
        }
    }

    public function numbersAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // get the booking ref prefix
        $bookingrefprefix = $this->container->getParameter('bookingrefprefix');

        // Grab current purchase
        $purchase = $booking->getPurchase($service->getId(), $code, $bookingrefprefix);

        // create form
        $numberstype = new NumbersType($service->getMaxparty());
        $form   = $this->createForm($numberstype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check numbers
                $adults = $purchase->getAdults();
                $children = $purchase->getChildren();
                if (($adults + $children) > $service->getMaxparty()) {
                    $form->get('adults')->addError(new FormError('Total party size is more than '.$service->getMaxparty()));
                } else if (($adults<1) or ($adults>$service->getMaxparty()) or ($children<0) or ($children>$service->getMaxparty())) {
                    $form->get('adults')->addError(new FormError('Value supplied out of range.'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_joining'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:numbers.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

    public function joiningAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // get the joining stations
        $stations = $em->getRepository('SRPSBookingBundle:Joining')
            ->findByServiceid($service->getId());
        if (!$stations) {
            throw new \Exception('No joining stations found for this service');
        }

        // If there is only one then there is nothing to do
        if (count($stations)==1) {
            $station = array_pop($stations);
            $purchase->setJoining($station->getCrs());
            $em->persist($purchase);
            $em->flush();

            return $this->redirect($this->generateUrl('booking_destination'));
        }

        // create form
        $joiningtype = new JoiningType($stations);
        $form   = $this->createForm($joiningtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check that we have a value of some sort
                if (!$purchase->getJoining()) {
                    $form->get('joining')->addError(new FormError('You must choose a joining station'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_destination'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:joining.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

    public function destinationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // get the destinations
        $destinations = $em->getRepository('SRPSBookingBundle:Destination')
            ->findByServiceid($service->getId());
        if (!$destinations) {
            throw new \Exception('No destinations found for this service');
        }

        // If there is only one then there is nothing to do
        if (count($destinations)==1) {
            $destination = array_pop($destinations);
            $purchase->setDestination($destination->getCrs());
            $em->persist($purchase);
            $em->flush();

            return $this->redirect($this->generateUrl('booking_meals'));
        }

        // we'll build up a set of data to display all the useful info in the
        // form... so bear with me
        $joiningcrs = $purchase->getJoining();
        $joining = $em->getRepository('SRPSBookingBundle:Joining')
            ->findOneBy(array('crs'=>$joiningcrs, 'serviceid'=>$service->getId()));
        $pricebandgroupid = $joining->getPricebandgroupid();
        $dests = array();
        foreach ($destinations as $destination) {
            $priceband = $em->getRepository('SRPSBookingBundle:Priceband')
                ->findOneBy(array('pricebandgroupid'=>$pricebandgroupid, 'destinationid'=>$destination->getId()));
            if (!$priceband) {
                throw new \Exception( "No priceband for pbgid=$pricebandgroupid did=".$destination->getId());
            }
            $dest = new \stdClass();
            $dest->crs = $destination->getCrs();
            $dest->description = $destination->getDescription();
            $dest->first = $priceband->getFirst();
            $dest->standard = $priceband->getStandard();
            $dest->child = $priceband->getChild();
            $dests[] = $dest;
        }

        // create form
        $destinationtype = new DestinationType($destinations);
        $form   = $this->createForm($destinationtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check that we have a value of some sort
                if (!$purchase->getDestination()) {
                    $form->get('destination')->addError(new FormError('You must make a choice'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_meals'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:destination.html.twig', array(
            'purchase' => $purchase,
            'destinations' => $dests,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

   public function mealsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // get the joining station (to see what meals available)
        $station = $em->getRepository('SRPSBookingBundle:Joining')
            ->findOneBy(array('serviceid'=>$service->getId(), 'crs'=>$purchase->getJoining()));
        if (!$station) {
            throw new \Exception('No joining stations found for this service');
        }

        // Get the passenger count
        $passengercount = $purchase->getAdults() + $purchase->getChildren();

        // we need to know about the number
        $numbers = $booking->countStuff($service->getId());

        // create form
        $mealstype = new MealsType($station, $service, $numbers, $passengercount);
        $form   = $this->createForm($mealstype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check that we have a value of some sort
                if (!$purchase->getJoining()) {
                    $form->get('joining')->addError(new FormError('You must choose a joining station'));
                } else {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_class'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:meals.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

   public function classAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // Get the passenger count
        $passengercount = $purchase->getAdults() + $purchase->getChildren();

        // get first and standard fares
        $farestandard = $booking->calculateFare($service, $purchase, 'S');
        $farefirst = $booking->calculateFare($service, $purchase, 'F');

        // we need to know about the number
        // it's a bodge - but if the choice is made then skip this check
        $numbers = $booking->countStuff($service->getId(), $purchase);
        $availablefirst = $numbers->getRemainingfirst() >= $passengercount;
        $availablestandard = $numbers->getRemainingstandard() >= $passengercount;

        // create form
        $classtype = new ClassType();
        $form   = $this->createForm($classtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // check that we have a valid response
                $class = $purchase->getClass();
                if (($class=='F' and $availablefirst) or ($class=='S' and $availablestandard)) {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_additional'));

                } else {
                    $form->get('class')->addError(new FormError('You must make a selection'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:class.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'farefirst' => $farefirst,
            'farestandard' => $farestandard,
            'availablefirst' => $availablefirst,
            'availablestandard' => $availablestandard,
            'form'   => $form->createView(),
        ));
    }
    
    public function additionalAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }
        
        // current counts
        $numbers = $booking->countStuff($service->getId(), $purchase); 
        
        // Get the passenger count
        $passengercount = $purchase->getAdults() + $purchase->getChildren();        
        
        // This page will only be shown if we are going to ask about firstsingle
        // option, or ask for comments.
        $iscomments = $service->isCommentbox();
        $issupplement = ($numbers->getRemainingfirstsingles() >= $passengercount) 
                && ($purchase->getClass()=='F')
                && (($passengercount==1) || ($passengercount==2))
                ;
        if (!($iscomments or $issupplement)) {
                return $this->redirect($this->generateUrl('booking_personal'));            
        }

        // create form
        $classtype = new AdditionalType($iscomments, $issupplement);
        $form   = $this->createForm($classtype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // 'save' the purchase
                $em->persist($purchase);
                $em->flush();

                return $this->redirect($this->generateUrl('booking_personal'));
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:additional.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'iscomments' => $iscomments,
            'issupplement' => $issupplement,
            'form'   => $form->createView(),
        ));
    }

    public function personalAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // create form
        $personaltype = new PersonalType();
        $form   = $this->createForm($personaltype, $purchase);

        // submitted?
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                // need to check fields the hard way
                $error = false;
                if (!$purchase->getSurname()) {
                    $form->get('surname')->addError(new FormError('Surname is required'));
                    $error = true;
                }
                if (!$purchase->getFirstname()) {
                    $form->get('firstname')->addError(new FormError('First name is required'));
                    $error = true;
                }
                if (!$purchase->getAddress1()) {
                    $form->get('address1')->addError(new FormError('Address line 1 is required'));
                    $error = true;
                }
                if (!$purchase->getCity()) {
                    $form->get('city')->addError(new FormError('Post town/city is required'));
                    $error = true;
                }
                if (!$purchase->getPostcode()) {
                    $form->get('postcode')->addError(new FormError('Postcode is required'));
                    $error = true;
                }
                if (!$purchase->getEmail()) {
                    $form->get('email')->addError(new FormError('Email is required'));
                    $error = true;
                }
                
                // Now need to 'normalise' some of the fields
                $purchase->setTitle(ucwords($purchase->getTitle()));                
                $purchase->setSurname(ucwords($purchase->getSurname()));
                $purchase->setFirstname(ucwords($purchase->getFirstname()));
                $purchase->setAddress1(ucwords($purchase->getAddress1()));
                $purchase->setAddress2(ucwords($purchase->getAddress2())); 
                $purchase->setCity(ucwords($purchase->getCity()));  
                $purchase->setCounty(ucwords($purchase->getCounty()));                
                $purchase->setPostcode(strtoupper($purchase->getPostcode())); 
                $purchase->setEmail(strtolower($purchase->getEmail()));

                if (!$error) {
                    $em->persist($purchase);
                    $em->flush();

                    return $this->redirect($this->generateUrl('booking_review'));
                }
            }
        }

        // display form
        return $this->render('SRPSBookingBundle:Booking:personal.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'form'   => $form->createView(),
        ));
    }

   public function reviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $this->get('srps_booking');
        $sagepay = $this->get('srps_sagepay');

        // initialise sagepay thingy
        // (needs to access a bunch of params)
        $sagepay->setParameters($this->container);

        // Grab current purchase
        $purchase = $booking->getPurchase();

        // We have to mark done here, there isn't another chance
        // before passing control to SagePay (and it will get deleted if
        // something goes wrong
        $purchase->setCompleted(true);
        $em->persist($purchase);
        $em->flush();

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // work out final fare
        $fare = $booking->calculateFare($service, $purchase, $purchase->getClass());
        $purchase->setPayment($fare->total);
        $em->persist($purchase);
        $em->flush();

        // get the destination
        $destination = $em->getRepository('SRPSBookingBundle:Destination')
            ->findOneBy(array('serviceid'=>$service->getId(), 'crs'=>$purchase->getDestination()));

        // get the joining station
        $joining = $em->getRepository('SRPSBookingBundle:Joining')
            ->findOneBy(array('serviceid'=>$service->getId(), 'crs'=>$purchase->getJoining()));

        // get stuff for sagepay (must be absolute)
        $callbackurl = $this->generateUrl('booking_callback', array(), true);
        $sage = $sagepay->getSage($service, $purchase, $destination, $joining, $callbackurl, $fare);

        // display form
        return $this->render('SRPSBookingBundle:Booking:review.html.twig', array(
            'purchase' => $purchase,
            'code' => $code,
            'service' => $service,
            'destination' => $destination,
            'joining' => $joining,
            'sage' => $sage,
            'fare' => $fare,
        ));
    }

    public function callbackAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sagepay = $this->get('srps_sagepay');

        // initialise sagepay thingy
        // (needs to access a bunch of params)
        $sagepay->setParameters($this->container);

        // get the SagePay response object
        if (empty($_REQUEST['crypt'])) {
            // it's gone wrong... what do we do?
            throw new \Exception( 'No or empty crypt field on callback from SagePay');
        }
        $crypt = $_REQUEST['crypt'];

        // unscramble the data
        $sage = $sagepay->decrypt( $crypt );

        // get the important data
        $bookingref = $sage->VendorTxCode;

        // get the purchase
        $purchase = $em->getRepository('SRPSBookingBundle:Purchase')
            ->findOneBy(array('bookingref'=>$bookingref));

        if (!$purchase) {
            throw new \Exception( 'Unable to find record of booking '.$bookingref);
        }

        // Get the service object
        $code = $purchase->getCode();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->findOneByCode($code);
        if (!$service) {
            throw $this->createNotFoundException('Unable to find code ' . $code);
        }

        // get the destination
        $destination = $em->getRepository('SRPSBookingBundle:Destination')
            ->findOneBy(array('crs'=>$purchase->getDestination(), 'serviceid'=>$service->getId()));

        // get the joining station
         $joining = $em->getRepository('SRPSBookingBundle:Joining')
            ->findOneBy(array('crs'=>$purchase->getJoining(), 'serviceid'=>$service->getId()));

        // Regardless, record the bookingdata
        $purchase->setStatus($sage->Status);
        $purchase->setStatusdetail($sage->StatusDetail);
        $purchase->setCardtype($sage->CardType);
        $purchase->setLast4digits($sage->Last4Digits);
        $purchase->setBankauthcode($sage->BankAuthCode);
        $purchase->setDeclinecode($sage->DeclineCode);
        $purchase->setCompleted(true);
        $em->persist($purchase);
        $em->flush();

        // send emails
        $message = \Swift_Message::newInstance();
        $message->setFrom('noreply@srps.org.uk');
        $message->setTo($purchase->getEmail());
        $message->setContentType('text/html');

        if ($sage->Status=='OK') {
            $message->setSubject('Confirmation of SRPS Railtour Booking - ' . $service->getName())
                ->setBody(
                    $this->renderView(
                        'SRPSBookingBundle:Email:confirm.html.twig',
                        array(
                            'purchase' => $purchase,
                            'service' => $service,
                            'joining' => $joining,
                            'destination' => $destination,
                            ),
                        'text/html'
                    )
                )
                ->addPart(
                    $this->renderView(
                        'SRPSBookingBundle:Email:confirm.txt.twig',
                        array(
                            'purchase' => $purchase,
                            'service' => $service,
                            'joining' => $joining,
                            'destination' => $destination,
                            ),
                        'text/plain'
                    )
                );
        } else {

            // Status != OK, so the payment failed
             $message->setSubject('Failure Notice: SRPS Railtour Booking - ' . $service->getName())
                ->setBody(
                    $this->renderView(
                        'SRPSBookingBundle:Email:fail.html.twig',
                        array(
                            'purchase' => $purchase,
                            'service' => $service,
                            'joining' => $joining,
                            'destination' => $destination,
                            ),
                        'text/html'
                    )
                )
                ->addPart(
                    $this->renderView(
                        'SRPSBookingBundle:Email:fail.txt.twig',
                        array(
                            'purchase' => $purchase,
                            'service' => $service,
                            'joining' => $joining,
                            'destination' => $destination,
                            ),
                        'text/plain'
                    )
                );
        }
        $this->get('mailer')->send($message);
        
        // also send to backup (if defined)
        if ($this->container->hasParameter('srpsbackupemail')) {
            
            // where we send the backup email
            $srpsbackupemail = $this->container->getParameter('srpsbackupemail');
        
            $message->setTo($srpsbackupemail);
            $message->setSubject($service->getCode().'-'.$purchase->getBookingref());
            $this->get('mailer')->send($message);
        }

        // display form
        if ($sage->Status == 'OK') {
            return $this->render('SRPSBookingBundle:Booking:callback.html.twig', array(
                'purchase' => $purchase,
                'service' => $service,
                'sage' => $sage,
            ));
        } else {
            return $this->render('SRPSBookingBundle:Booking:decline.html.twig', array(
                'purchase' => $purchase,
                'service' => $service,
                'sage' => $sage,
            ));
        }
    }
}

