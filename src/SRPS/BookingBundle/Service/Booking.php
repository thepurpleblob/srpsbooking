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

}
