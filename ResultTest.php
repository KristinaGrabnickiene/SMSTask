<?php
/**
 * Created by PhpStorm.
 * User: Kristina
 * Date: 10/13/2018
 * Time: 10:15 PM
 */
include(dirname(__FILE__)."/sms.php");
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{


    public function testGetIncomeTotal()
    {
        $test1 =[5, 5, 5];
        $this->assertEquals(15, $test1->getPricesTotal());
    }


}
