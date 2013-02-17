<?php

namespace SRPS\BookingBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use SRPS\BookingBundle\Entity\Purchase;

class Booking
{

    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    /**
     * Create the table to display price band group
     * @param integer $pricebandgroupid
     */
    public function createPricebandTable($pricebandgroupid) {
        $em = $this->em;

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
     * Is destination used?
     * Checks if destination can be deleted
     * @param object $destination
     * @return boolean true if used
     */
    public function isDestinationUsed($destination) {
        $em = $this->em;

        // find pricebands that specify this destination
        $pricebands = $em->getRepository('SRPSBookingBundle:Priceband')
            ->findByDestinationid($destination->getId());

        // if there are non then not used
        if (!$pricebands) {
            return false;
        }

        // otherwise, all prices MUST be 0
        foreach ($pricebands as $priceband) {
            if (($priceband->getFirst()>0) or ($priceband->getStandard()>0)
                    and ($priceband->getChild()>0)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Is the priceband group assigned
     * in any joining station
     * @param object $pricebandgroup
     */
    public function isPricebandUsed($pricebandgroup) {
        $em = $this->em;

        // find joining stations that specify this group
        $joinings = $em->getRepository('SRPSBookingBundle:Joining')
            ->findByPricebandgroupid($pricebandgroup->getId());

        // if there are any then it is used
        if ($joinings) {
            return true;
        }

        return false;
    }

    /**
     * Clear the current session data and delete any expired purchases
     */
    public function cleanPurchases() {
        $em = $this->em;

        // Get the session
        $session = new Session();
        $session->start();

        // remove the key and the purchaseid
        $session->remove('key');
        $session->remove('purchaseid');

        // get incomplete purchases older than 1 hour
        $oldtime = time() - 3600;
        $query = $em->createQuery("SELECT p FROM SRPSBookingBundle:Purchase p
            WHERE p.completed=0 AND p.timestamp < :oldtime")
            ->setParameter('oldtime', $oldtime);
        $purchases = $query->getResult();

        if ($purchases) {
            foreach ($purchases as $purchase) {
                $em->remove($purchase);
            }
            $em->flush();
        }
    }

    /**
     * Find the current purchase record and/or create a new one if
     * needed
     */
    public function getPurchase($code='') {
        $em = $this->em;

        // See if the purchase session attribute exists
        $session = new Session();
        $session->start();
        $session->migrate();
        if ($key = $session->get('key')) {

            // then we should have the record id and they should match
            if ($purchaseid = $session->get('purchaseid')) {
                $purchase = $em->getRepository('SRPSBookingBundle:Purchase')
                    ->find($purchaseid);

                // If no purchase record despite id
                if (!$purchase) {
                    throw new \Exception('Purchase record not found');
                }

                // if it exists then the key must match (security I think)
                if ($purchase->getSesKey() != $key) {
                    throw new \Exception('Purchase key does not match session');
                } else {

                    // All is well. Return the record
                    $purchase->setTimestamp(time());
                    return $purchase;
                }
            } else {

                 // if record id isn't there then this is an exception
                throw new \Exception('Purchase id is missing in session');
            }
        }

        // If we get here, there is no session set up, so
        // there won't be a purchase record either

        // if no code was supplied then we are not allowed a new one
        if (empty($code)) {
            throw new \Exception('The purchase record was not found');
        }

        // create a random new key
        $key = sha1(microtime(true).mt_rand(10000,90000));

        // create the new purchase object
        $purchase = new Purchase();
        $purchase->setSeskey($key);
        $purchase->setCode($code);
        $purchase->setCreated(time());
        $purchase->setTimestamp(time());

        // and persist it
        $em->persist($purchase);
        $em->flush();

        // id should be set automagically
        $id = $purchase->getId();
        $session->set('key', $key);
        $session->set('purchaseid', $id);

        // we can add the booking ref (generated from id) - it should get
        // just need another persist to make sure
        $purchase->setBookingref('OL'.$id);
        $em->persist($purchase);
        $em->flush();

        return $purchase;
    }

}
