<?php
namespace UserAccountBundle\Serializer\Normalizer;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use UserAccountBundle\Entity\User;

class UserNormalizer implements NormalizerInterface{

    public function __construct(private Security $security, private ObjectNormalizer $normalizer){}
    
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        if(
            array_key_exists("groups", $context) && 
            count($context["groups"]) > 0 &&        //At least one group 
            $object == $this->security->getUser()
        ) $context["groups"][] = "user:read:owner";
        
        return $this->normalizer->normalize($object, $format, $context);        
    }

}