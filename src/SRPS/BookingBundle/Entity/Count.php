<?php

namespace SRPS\BookingBundle\Entity;

class Count {

    protected $bookedfirst;

    protected $bookedstandard;
    
    protected $bookedfirstsingles;

    protected $bookedmeala;

    protected $bookedmealb;

    protected $bookedmealc;

    protected $bookedmeald;

    protected $pendingfirst;

    protected $pendingstandard;
    
    protected $pendingfirstsingles;

    protected $pendingmeala;

    protected $pendingmealb;

    protected $pendingmealc;

    protected $pendingmeald;

    protected $remainingfirst;

    protected $remainingstandard;
    
    protected $remainingfirstsingles;

    protected $remainingmeala;

    protected $remainingmealb;

    protected $remainingmealc;

    protected $remainingmeald;

    public function __construct() {
        $this->bookedfirst = 0;
        $this->bookedstandard = 0;
        $this->bookedfirstsingles = 0;
        $this->bookedmeala = 0;
        $this->bookedmealb = 0;
        $this->bookedmealc = 0;
        $this->bookedmeald = 0;
        $this->pendingfirst = 0;
        $this->pendingstandard = 0;
        $this->pendingfirstsingles = 0;
        $this->pendingmeala = 0;
        $this->pendingmealb = 0;
        $this->pendingmealc = 0;
        $this->pendingmeald = 0;
        $this->remainingfirst = 0;
        $this->remainingstandard = 0;
        $this->remainingfirstsingles = 0;
        $this->remainingmeala = 0;
        $this->remainingmealb = 0;
        $this->remainingmealc = 0;
        $this->remainingmeald = 0;
    }

    public function setBookedfirst($bookedfirst) {
        $this->bookedfirst = $bookedfirst;
    }

    public function getBookedfirst() {
        return $this->bookedfirst;
    }

    public function setBookedstandard($bookedstandard) {
        $this->bookedstandard = $bookedstandard;
    }

    public function getBookedstandard() {
        return $this->bookedstandard;
    }

    public function setBookedfirstsingles($bookedfirstsingles) {
        $this->bookedfirstsingles = $bookedfirstsingles;
    }

    public function getBookedfirstsingles() {
        return $this->bookedfirstsingles;
    }    
    
    public function setBookedmeala($bookedmeala) {
        $this->bookedmeala = $bookedmeala;
    }

    public function getBookedmeala() {
        return $this->bookedmeala;
    }

    public function setBookedmealb($bookedmealb) {
        $this->bookedmealb = $bookedmealb;
    }

    public function getBookedmealb() {
        return $this->bookedmealb;
    }

    public function setBookedmealc($bookedmealc) {
        $this->bookedmealc = $bookedmealc;
    }

    public function getBookedmealc() {
        return $this->bookedmealc;
    }

    public function setBookedmeald($bookedmeald) {
        $this->bookedmeald = $bookedmeald;
    }

    public function getBookedmeald() {
        return $this->bookedmeald;
    }

    public function setPendingfirst($pendingfirst) {
        $this->pendingfirst = $pendingfirst;
    }

    public function getPendingfirst() {
        return $this->pendingfirst;
    }

    public function setPendingstandard($pendingstandard) {
        $this->pendingstandard = $pendingstandard;
    }

    public function getPendingstandard() {
        return $this->pendingstandard;
    }
    
    public function setPendingfirstsingles($pendingfirstsingles) {
        $this->pendingfirstsingles = $pendingfirstsingles;
    }

    public function getPendingfirstsingles() {
        return $this->pendingfirstsingles;
    }      

    public function setPendingmeala($pendingmeala) {
        $this->pendingmeala = $pendingmeala;
    }

    public function getPendingmeala() {
        return $this->pendingmeala;
    }

    public function setPendingmealb($pendingmealb) {
        $this->pendingmealb = $pendingmealb;
    }

    public function getPendingmealb() {
        return $this->pendingmealb;
    }

    public function setPendingmealc($pendingmealc) {
        $this->pendingmealc = $pendingmealc;
    }

    public function getPendingmealc() {
        return $this->pendingmealc;
    }

    public function setPendingmeald($pendingmeald) {
        $this->pendingmeald = $pendingmeald;
    }

    public function getPendingmeald() {
        return $this->pendingmeald;
    }

    public function setRemainingfirst($remainingfirst) {
        $this->remainingfirst = $remainingfirst;
    }

    public function getRemainingfirst() {
        return $this->remainingfirst;
    }

    public function setRemainingstandard($remainingstandard) {
        $this->remainingstandard = $remainingstandard;
    }

    public function getRemainingstandard() {
        return $this->remainingstandard;
    }
    
    public function setRemainingfirstsingles($remainingfirstsingles) {
        $this->remainingfirstsingles = $remainingfirstsingles;
    }

    public function getRemainingfirstsingles() {
        return $this->remainingfirstsingles;
    }      

    public function setRemainingmeala($remainingmeala) {
        $this->remainingmeala = $remainingmeala;
    }

    public function getRemainingmeala() {
        return $this->remainingmeala;
    }

    public function setRemainingmealb($remainingmealb) {
        $this->remainingmealb = $remainingmealb;
    }

    public function getRemainingmealb() {
        return $this->remainingmealb;
    }

    public function setRemainingmealc($remainingmealc) {
        $this->remainingmealc = $remainingmealc;
    }

    public function getRemainingmealc() {
        return $this->remainingmealc;
    }

    public function setRemainingmeald($remainingmeald) {
        $this->remainingmeald = $remainingmeald;
    }

    public function getRemainingmeald() {
        return $this->remainingmeald;
    }
}
