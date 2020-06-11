<?php

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\IsColourForCategorieValidator;
use App\Validator\Constraints\IsColourForCategorie;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class IsColourForCategorieValidatorTest extends TestCase
{
    public function getValidator($expectedViolation=false){
        $validator = new IsColourForCategorieValidator();

        $context = $this->getMockBuilder
        (ExecutionContextInterface::class)->getMock();

        if ($expectedViolation){
            $violation=$this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation->expects($this->any())->method('setParameter')
                ->willReturn($violation);
            $violation->expects($this->once())->method('addViolation');
            $context->expects($this->once())
                ->method('buildViolation')
                ->willReturn($violation);
        }
        else{
            $context->expects($this->never())->method('buildViolation');
        }
        $validator->initialize($context);
        return $validator;
    }
    public function testBadColorForCategorie(){
        $constraint = new IsColourForCategorie();
        $this->getValidator(true)->validate("nom de couleur qui sera jamais pris en charge",$constraint);
    }

    public function testGoodColorForCategorie(){
        $constraint = new IsColourForCategorie();
        $this->getValidator(false)->validate("blue",$constraint);
    }
}