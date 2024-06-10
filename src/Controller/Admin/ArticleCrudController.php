<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Bundle\SecurityBundle\Security;

class ArticleCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('name', 'Nom'),
            NumberField::new('price', 'Prix')
                ->formatValue(function ($value, $entity) {
                    return number_format($value, 2, ',', '') . ' €';
                }),
            AssociationField::new('category', 'Catégorie')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if ($value !== null) {
                        return $value->getId();
                    } else {
                        return ''; 
                    }
                }),
            
            AssociationField::new('category', 'Catégorie')
                ->onlyWhenCreating()
                ->autocomplete(),
            AssociationField::new('category', 'Catégorie')
                ->onlyWhenUpdating()
                ->autocomplete(),
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
