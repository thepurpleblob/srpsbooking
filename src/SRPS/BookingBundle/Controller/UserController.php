<?php

namespace SRPS\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SRPS\BookingBundle\Entity\User;

class UserController extends Controller
{
    
    public function installAction() 
    {
        $em = $this->getDoctrine()->getManager();

        // Get all our users
        $users = $em->getRepository('SRPSBookingBundle:User')
            ->findAll();
        
        // if there are none, we will create the default admin user
        if (!$users) {
            $user = new User;
            $user->setFirstname('admin');
            $user->setLastname('admin');
            $user->setUsername('admin');
            $user->setPassword('admin');
            $user->setRole('ROLE_ADMIN');
            $em->persist($user);
            $em->flush();
        }
        
        // regardless go to users screen
        return $this->redirect($this->generateUrl('admin_user_index'));
    }
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get all our users
        $users = $em->getRepository('SRPSBookingBundle:User')
            ->findAll();

        return $this->render('SRPSBookingBundle:User:index.html.twig', array(
            'users' => $users,
        ));
    }
    
    public function editAction($username) {
        
        $em = $this->getDoctrine()->getManager();
        
        // find the user
        $user = $em->getRepository('SRPSBookingBundle:User')
            ->findOneBy(array('username'=>$username));  
        if (!$user) {
            thrown new \Exception("User $username not found in db");
        }
    }

    public function viewAction($purchaseid)
    {
        $em = $this->getDoctrine()->getManager();

        // Get the purchase record
        $purchase = $em->getRepository('SRPSBookingBundle:Purchase')
            ->find($purchaseid);
        if (!$purchase) {
            throw new \Exception('purchase item could not be found');
        }

        // Get the service object
        $serviceid = $purchase->getServiceid();
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);
        if (!$service) {
            throw new \Exception('Unable to find service');
        }

        return $this->render('SRPSBookingBundle:Report:view.html.twig', array(
            'service' => $service,
            'purchase' => $purchase,
        ));
    }
    
    /**
     * The service id can also be the code
     */
    public function exportAction($serviceid) {
        $em = $this->getDoctrine()->getManager();
        $reports = $this->get('srps_reports');
        
        // Get the service object
        $service = $em->getRepository('SRPSBookingBundle:Service')
            ->find($serviceid);
        if (!$service) {
            $service = $em->getRepository('SRPSBookingBundle:Service')
                ->findOneBy(array('code'=>$serviceid));
            if (!$service) {
                throw new \Exception('Unable to find service');
            }
            $serviceid = $service->getId();
        }   
        
        // Get the purchases
        $purchases = $em->getRepository('SRPSBookingBundle:Purchase')
            ->findBy(array(
                'serviceid' => $serviceid,
                'completed' => true,
                'status' => 'OK',
                ));
        
        // if there are none, then nothing to do
        if (!$purchases) {
            return $this->redirect($this->generateUrl('admin'));
        }
        
        // Create a filename
        $filename = "rt-bkg-".$service->getCode().'.csv';
        
        // create the response
        $response = new Response();
        $response->setContent($reports->getExport($purchases));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"'); 
        
        return $response;
    }
}

