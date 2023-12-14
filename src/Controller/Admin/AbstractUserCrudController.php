<?php

namespace UserAccountBundle\Controller\Admin;

use RootBundle\Controller\Admin\AbstractCrudController;
use UserAccountBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

abstract class AbstractUserCrudController extends AbstractCrudController
{

    public function configureFields(string $pageName): iterable
    {
        return array_merge(parent::configureFields($pageName), [
            "gender" => ChoiceField::new("gender")->setChoices(array_flip(User::GENDERS)),
            "avatarFile" => $this->newImageField(User::AVATARS_FOLDER, "avatarFile", "Avatar"),
            "phoneNumber" => NumberField::new("phoneNumber")
        ]);
    }
    protected function fieldsToHideOnIndex(): array
    {
        return ["birthDate"];
    }

    protected function fieldsToHideOnForm(): array
    {
        return array_merge(parent::fieldsToHideOnForm(), ["emailValidated"]);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(!preg_match("#^[+-/=\w]{88}$#", $entityInstance->getPassword())) // Mot de passe non hashé
            $entityInstance->setPassword(User::hashPassword($entityInstance->getPassword()));
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add("lastName")
            ->add("firstName")
            ->add("userName")
        ;
    }

}
