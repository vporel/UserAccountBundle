<?php

namespace UserAccountBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use UserAccountBundle\Entity\AdminInterface;

/**
 * Controller gérant la création, modification des comptes administrateurs
 */
abstract class AbstractAdminCrudController extends AbstractUserCrudController
{
    
    public function configureFields(string $pageName): iterable
    {
        $superField = BooleanField::new("super");
        $topLevelField = BooleanField::new("topLevel");
        if(!$this->getUser()->isTopLevel()){
            $superField->hideOnForm();
            $topLevelField->hideOnForm();
        }
        return array_merge(parent::configureFields($pageName), [
            $superField,
            $topLevelField
        ]);
       
    }

    protected function fieldsToHide(): array
    {
        return ["emailValidated", "super", "topLevel"];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->update(Crud::PAGE_INDEX, Action::DELETE, function(Action $action){
                return $action->displayIf(function(AdminInterface $entity){
                    return ($this->getUser()->isTopLevel() && !$entity->isTopLevel()) || ($this->getUser()->isSuper() && !$entity->isSuper());
                });
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function(Action $action){
                return $action->displayIf(function(AdminInterface $entity){
                    $user = $this->getUser();
                    return $entity->getId() == $user->getId() || ($user->isTopLevel() && !$entity->isTopLevel()) || ($user->isSuper() && !$entity->isSuper());
                });
            });
        if(!$this->getUser()->isSuper()){
            $actions->remove(Crud::PAGE_INDEX, Action::NEW);
        }
        return $actions;
    }
}
