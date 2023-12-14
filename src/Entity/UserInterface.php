<?php 
namespace UserAccountBundle\Entity;


/**
 * This interface can be used by other bundles as target entity
 * since the abstract class 'User' is not allowed by doctrine
 * However the applicaitions will have to define the configuration key "resolve_target_entities"
 */
interface UserInterface{}