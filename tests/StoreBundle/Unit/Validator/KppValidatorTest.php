<?php

namespace StoreBundle\Unit\Validator;


use PHPUnit\Framework\MockObject\MockObject;
use StoreBundle\Validator\Constraints\Kpp;
use Tests\StoreBundle\StoreWebTestCase;

class KppValidatorTest extends StoreWebTestCase
{
  /**
   * @dataProvider kppProvider
   */
  public function testValidate($ogrn, $result)
  {
    $context = $this->getMockExecutionContext();
    $constraint = new Kpp();
    $validator = $this->getClient()->getContainer()->get('validator.validator_factory')->getInstance($constraint);
    $validator->initialize($context);

    $context->expects($result?$this->never():$this->once())->method('addViolation');

    $validator->validate($ogrn, $constraint);
  }

  public function kppProvider()
  {
    return [
      ['', false],
      ['123', false],
      ['123456', false],
      ['1234567891', false],
      ['1234567891156', false],
      ['kpp', false],
      ['632401001', true],
      ['222501001', true],
      ['220401001', true],
      ['741501001', true],
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