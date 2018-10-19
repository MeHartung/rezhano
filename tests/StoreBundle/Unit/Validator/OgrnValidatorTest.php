<?php

namespace StoreBundle\Unit\Validator;


use PHPUnit\Framework\MockObject\MockObject;
use StoreBundle\Validator\Constraints\Ogrn;
use StoreBundle\Validator\Constraints\OgrnValidator;
use Tests\StoreBundle\StoreWebTestCase;

class OgrnValidatorTest extends StoreWebTestCase
{
  /**
   * @dataProvider ogrnProvider
   */
  public function testValidate($ogrn, $result)
  {
    $context = $this->getMockExecutionContext();
    $constraint = new Ogrn();
    $validator = $this->getClient()->getContainer()->get('validator.validator_factory')->getInstance($constraint);
    $validator->initialize($context);

    $context->expects($result?$this->never():$this->once())->method('addViolation');

    $validator->validate($ogrn, $constraint);
  }

  public function ogrnProvider()
  {
    return [
      ['', false],
      ['123456789', false],
      ['1234567894561', false],
      ['9512345484845', false],
      ['951234548484551', false],
      ['456189151564514', false],
      ['1036303288955', true],
      ['1072204003190', true],
      ['1060408005437', true],
      ['1027400872718', true],
      ['305770000292186', true],
      ['305770000228412', true],
      ['305770002381178', true],
      ['314774605701032', true],
      ['307770000367755', true],
    ];
  }

  /**
   * @return MockObject|\Symfony\Component\Validator\Context\ExecutionContextInterface
   */
  private function getMockExecutionContext()
  {
    $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContextInterface')
      ->disableOriginalConstructor()
      ->getMock()
    ;
    return $context;
  }
}