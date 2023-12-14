<?php
namespace UserAccountBundle\Validator;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

#[\Attribute]
class PhoneNumber extends Regex{
    public function __construct($options = [])
    {
        $options["pattern"] = "/^((00|\+)?237)?[62][56789][0-9]{7}$/";
        $options["message"] = $options["message"] ?? "Format non valide (MTN, Orange Nexttel, Camtel)";
        parent::__construct($options);
    }

    public function validatedBy(): string
    {
        return RegexValidator::class;
    }
}