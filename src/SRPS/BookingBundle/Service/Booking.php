<?php

namespace SRPS\BookingBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use SRPS\BookingBundle\Entity\Purchase;
use SRPS\BookingBundle\Entity\Count;

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
    public function getPurchase($serviceid, $code='') {
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
        $purchase->setServiceid($serviceid);
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

    /**
     * Convert a null to a zero
     * (done a lot in countStuff)
     */
    private function zero($value) {
        $result = ($value) ? $value : 0;
        return $result;
    }

    /**
     * Count the purchases and work out what's left. Major PITA this
     */
    public function countStuff($serviceid) {
        $em = $this->em;

        // Clear incomplete purchases older than 1 hour
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

        // get limits
        $limits = $em->getRepository('SRPSBookingBundle:Limits')
            ->findOneByServiceid($serviceid);

        // Create counts entity
        $count = new Count();

        // get first class booked
        $fbquery = $em->createQuery("SELECT (SUM(p.adults)+SUM(p.children)) AS a FROM SRPSBookingBundle:Purchase p
            WHERE p.completed=1 AND p.class='F' AND p.serviceid=$serviceid ");
        $fbtotal = $fbquery->getResult();
        $count->setBookedfirst($this->zero($fbtotal[0]['a']));

        // get first class in progress
        $fpquery = $em->createQuery("SELECT (SUM(p.adults)+SUM(p.children)) AS a FROM SRPSBookingBundle:Purchase p
            WHERE p.completed=0 AND p.class='F' AND p.serviceid=$serviceid ");
        $fptotal = $fpquery->getResult();
        $count->setPendingfirst($this->zero($fptotal[0]['a']));

        // firct class remainder is simply
        $count->setRemainingfirst($limits->getFirst() - $count->getBookedfirst() - $count->getPendingfirst());

        // get standard class booked
        $sbquery = $em->createQuery("SELECT (SUM(p.adults)+SUM(p.children)) AS a FROM SRPSBookingBundle:Purchase p
            WHERE p.completed=1 AND p.class='S' AND p.serviceid=$serviceid ");
        $sbtotal = $sbquery->getResult();
        $count->setBookedstandard($this->zero($sbtotal[0]['a']));

        // get standard class in progress
        $spquery = $em->createQuery("SELECT (SUM(p.adults)+SUM(p.children)) AS a FROM SRPSBookingBundle:Purchase p
            WHERE p.completed=0 AND p.class='F' AND p.serviceid=$serviceid ");
        $sptotal = $spquery->getResult();
        $count->setPendingstandard($this->zero($sptotal[0]['a']));

        // firct class remainder is simply
        $count->setRemainingstandard($limits->getStandard() - $count->getBookedstandard() - $count->getPendingstandard());

        // Get booked meals
        $mbquery = $em->createQuery("SELECT SUM(p.meala) AS a, SUM(p.mealb) AS b,
            SUM(p.mealc) AS c, SUM(p.meald) AS d FROM SRPSBookingBundle:Purchase p
            WHERE p.completed=1 AND p.serviceid=$serviceid ");
        $mbtotal = $mbquery->getResult();
        $count->setBookedmeala($this->zero($mbtotal[0]['a']));
        $count->setBookedmealb($this->zero($mbtotal[0]['b']));
        $count->setBookedmealc($this->zero($mbtotal[0]['c']));
        $count->setBookedmeald($this->zero($mbtotal[0]['d']));

        // Get pending meals
        $mpquery = $em->createQuery("SELECT SUM(p.meala) AS a, SUM(p.mealb) AS b,
            SUM(p.mealc) AS c, SUM(p.meald) AS d FROM SRPSBookingBundle:Purchase p
            WHERE p.completed=0 AND p.serviceid=$serviceid ");
        $mptotal = $mpquery->getResult();
        $count->setPendingmeala($this->zero($mptotal[0]['a']));
        $count->setPendingmealb($this->zero($mptotal[0]['b']));
        $count->setPendingmealc($this->zero($mptotal[0]['c']));
        $count->setPendingmeald($this->zero($mptotal[0]['d']));

        // Get remaining meals
        $count->setRemainingmeala($limits->getMeala() - $count->getBookedmeala() - $count->getPendingmeala());
        $count->setRemainingmealb($limits->getMealb() - $count->getBookedmealb() - $count->getPendingmealb());
        $count->setRemainingmealc($limits->getMealc() - $count->getBookedmealc() - $count->getPendingmealc());
        $count->setRemainingmeala($limits->getMeald() - $count->getBookedmeald() - $count->getPendingmeald());

        return $count;
    }

}
