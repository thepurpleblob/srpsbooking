<?php

namespace SRPS\BookingBundle\Service;

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

}
