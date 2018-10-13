<?php

class SMSPlan
{
    private $smsPrice;
    private $smsValue;

    public function __construct( $smsPrice, $smsValue )
    {
        $this->smsPrice = $smsPrice;
        $this->smsValue = $smsValue;
    }

    public function getSmsValue()
    {
        return $this->smsValue;
    }

    public function getSmsPrice()
    {
        return $this->smsPrice;
    }
}

class Result
{

    private $pricesList = [];
    private $valuesList = [];

    public function getPricesList()
    {
        return $this->pricesList;
    }

    public function getPricesTotal()
    {
        return array_sum($this->pricesList);
    }

    public function getValuesTotal()
    {
        return array_sum($this->valuesList);
    }

    public function smsCount()
    {
        return count($this->pricesList);
    }

    public function addPlan( SMSPlan $smsPlan )
    {
        $this->pricesList[] = $smsPlan->getSmsPrice();
        $this->valuesList[] = $smsPlan->getSmsValue();
    }
}

/*SMS Planas */
$plans[] = new SMSPlan(0.5, 0.41);
$plans[] = new SMSPlan(1, 0.96);
$plans[] = new SMSPlan(2, 1.91);
$plans[] = new SMSPlan(3, 2.9);

//usort($plans,function(SMSPlan $a,SMSPlan $b){return $a->getSmsPrice() < $b->getSmsPrice();});

$finalResult = new Result();

function recursion( $valueRequired, Result $result,array $plans, Result &$finalResult )
{
    $valuesTotal = $result->getValuesTotal();
    foreach ($plans as $plan) {
        $resultCopy = clone $result;
        $resultCopy->addPlan($plan);
        if (($valuesTotal + $plan->getSmsValue()) >= $valueRequired) {
            if ($finalResult->getPricesTotal() > $resultCopy->getPricesTotal() || $finalResult->smsCount() == 0) {
                $finalResult = $resultCopy;
            } elseif (($finalResult->smsCount() > $resultCopy->smsCount())&&($finalResult->getPricesTotal() == $resultCopy->getPricesTotal())) {
                $finalResult = $resultCopy;
            }

        } else {
            recursion($valueRequired, $resultCopy, $plans, $finalResult);
        }
    }
}

set_time_limit(300);
$startTime = microtime(true);
recursion(11, new Result(), $plans, $finalResult);
$finishTime = microtime(true);
$since_start = $finishTime - $startTime;
echo $since_start . ' seconds <br>';

echo implode(' EUR, ', $finalResult->getPricesList()) . ' EUR = ' . $finalResult->getPricesTotal();


