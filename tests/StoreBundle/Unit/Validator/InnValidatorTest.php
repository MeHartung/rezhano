<?php

namespace StoreBundle\Unit\Validator;


use PHPUnit\Framework\MockObject\MockObject;
use StoreBundle\Validator\Constraints\Inn;
use Tests\StoreBundle\StoreWebTestCase;

class InnValidatorTest extends StoreWebTestCase
{
  /**
   * @dataProvider innProvider
   */
  public function testValidate($ogrn, $result)
  {
    $context = $this->getMockExecutionContext();
    $constraint = new Inn();
    $validator = $this->getClient()->getContainer()->get('validator.validator_factory')->getInstance($constraint);
    $validator->initialize($context);

    $context->expects($result?$this->never():$this->once())->method('addViolation');

    $validator->validate($ogrn, $constraint);
  }

  public function innProvider()
  {
    return [
      [null, false],
      ['', false],
      ['12345678', false],
      ['1234567890', false],
      ['123456789012', false],
      ['771521716712', true],
      ['772799338286', true],
      ['772345855075', true],
      ['772747550002', true],
      ['772770224286', true],
      ['6382046904', true],
      ['0408017018', true],
      ['2204029989', true],
      ['7415033630', true],
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