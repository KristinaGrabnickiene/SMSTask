<?php

class Result
{
    /**
     * @var array  sąrašas - SMS Kaina vartotojui
     */
    private $pricesList = [];
    /**
     * @var array sąrašas - SMS vertė
     */
    private $incomeList = [];

    /**
     * @return array sąrašo geteris
     */
    public function getPricesList()
    {
        return $this->pricesList;
    }

    /**
     * @return float|int kainų sąrašo suma vartotojui
     */
    public function getPricesTotal()
    {
        return array_sum($this->pricesList);
    }

    /**
     * @return float|int pajamų sąrašo suma
     */
    public function getIncomeTotal()
    {
        return array_sum($this->incomeList);

    }

    /**
     * @return int sms kiekis sąraše
     */
    public function smsCount()
    {
        return count($this->pricesList);
    }

    /**
     * @param array smsPlano masyvas
     */
    public function addPlanArray( array $smsPlan )
    {
        $this->pricesList[] = $smsPlan["price"];
        $this->incomeList[] = $smsPlan["income"];
    }
}

/**
 * @param $requiredIncome int reikalinga surinkti suma
 * @param $maxSMS int sms limitas
 * @param Result $result darbinis rezultatas
 * @param array $plans SmsPlano masyvas
 * @param Result $finalResult galutinio rezultato referencija
 */
function recursion( $requiredIncome, $maxSMS, Result $result, array $plans, Result &$finalResult )
{
//    $valuesTotal = $result->getIncomeTotal();

    foreach ($plans as $plan) {
        //Darom nauja rezultatų  kopiją kiekvienam ciklui
        $resultCopy = clone $result;
        $resultCopy->addPlanArray($plan);

        //Jei maxSMS skaičius pasiektas
        //bet nepasiekta reikalinga surinkti suma (requiredIncome)
        //išeinam iš rekursijos
        if (!($maxSMS > 0 && $maxSMS == $resultCopy->smsCount() && $resultCopy->getIncomeTotal() < $requiredIncome)) {

            //Tikriname ar gavom geresnį rezultatą
            //jei surinkom reikalinga sumą
            if ($resultCopy->getIncomeTotal() >= $requiredIncome) {

                //Jaigu galutinio rezultato kaina didesnė už dabar gautą rezultatą
                //arba galutinio rezultato dar neturim išsaugom kaip galutinį
                if ($finalResult->getPricesTotal() > $resultCopy->getPricesTotal() || $finalResult->smsCount() == 0) {
                    $finalResult = $resultCopy;
                } // jai galutiniam rezultate panaudota daugiau sms, nei dabartiniam ir abiejų rezultatų kainos vienodos ...
                elseif (($finalResult->smsCount() > $resultCopy->smsCount()) && ($finalResult->getPricesTotal() == $resultCopy->getPricesTotal())) {
                    $finalResult = $resultCopy;
                }
            } else {
                //Jai reikalinga suma nesurinkta, einam gilyn į rekursiją
                recursion($requiredIncome, $maxSMS, $resultCopy, $plans, $finalResult);
            }
        }
    }
}
/*Json failų nuskaitymas ir finalResult sukūrimas*/

$input = file_get_contents($argv[1]);
$inputJSON = json_decode($input, true);
$plans = $inputJSON["sms_list"];
$finalResult = new Result();
//Jai nepaduota max_messages , max_messages prilyginu 0
if ($inputJSON["max_messages"] == null) $inputJSON["max_messages"] = 0;

/* Pradedam recursion funkcija */
recursion($inputJSON["required_income"], $inputJSON["max_messages"], new Result(), $plans, $finalResult);

//Atsakymo pateikimas
$resultStr = "[" . implode(', ', $finalResult->getPricesList()) . ']';
fwrite(STDOUT, $resultStr);

