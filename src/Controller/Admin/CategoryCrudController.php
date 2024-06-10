<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Bundle\SecurityBundle\Security;
use  EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

  
    public function configureFields(string $pageName): iterable
    {
        return [
           TextField::new('name'),
        ];
    }


    public function configureActions(Actions $actions): Actions
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isSuperAdmin = $this->security->isGranted('ROLE_SUPER_ADMIN');
        $isEmployee = $this->security->isGranted('ROLE_EMPLOYEE');
        
        $actionsConfig = $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;

        if ($isEmployee || $isAdmin) {
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::EDIT);
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::DELETE);
        }

        return $actionsConfig;
    }
}
