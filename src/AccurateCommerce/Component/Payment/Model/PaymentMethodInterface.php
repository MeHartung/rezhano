<?php

namespace AccurateCommerce\Component\Payment\Model;

interface PaymentMethodInterface
{
   function getId();
   function getName();
   function getDescription();
   function getPosition();
   function isEnabled();
   function getAvailabilityDecisionManagerId();
   function setAvailabilityDecisionManagerId($availabilityDecisionManagerId);
   function getFeeCalculatorId();
   function setFeeCalculatorId($feeCalculatorId);
}

