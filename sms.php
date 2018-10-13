<?php
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

    public function addPlanArray( array $smsPlan )
    {
        $this->pricesList[] = $smsPlan[0];
        $this->valuesList[] = $smsPlan[1];
    }
}

/*SMS Planas */
$plans[] = [0.5, 0.41];
$plans[] = [1, 0.96];
$plans[] = [2, 1.91];
$plans[] = [3, 2.9];

$finalResult = new Result();

function recursion( $valueRequired, Result $result,array $plans, Result &$finalResult )
{
    $valuesTotal = $result->getValuesTotal();
    foreach ($plans as $plan) {
        $resultCopy = clone $result;
        $resultCopy->addPlanArray($plan);
        if (($valuesTotal + array_sum($plan)) >= $valueRequired) {
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
//$input = file_get_contents("imput.json");

$startTime = microtime(true);
recursion(11, new Result(), $plans, $finalResult);
$finishTime = microtime(true);
$since_start = $finishTime - $startTime;
echo $since_start . ' seconds <br>';

echo implode(' EUR, ', $finalResult->getPricesList()) . ' EUR = ' . $finalResult->getPricesTotal();


