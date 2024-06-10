<?php

namespace App\Controller\Admin;

use App\Entity\CommandLine;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Bundle\SecurityBundle\Security;

class CommandLineCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public static function getEntityFqcn(): string
    {
        return CommandLine::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            
            AssociationField::new('service')
                ->formatValue(function ($value, $entity) {
                    if ($value !== null) {
                        return $value->getName();
                    } else {
                        return null; 
                    }
                }),
            
                AssociationField::new('service', 'Prix HT')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if ($value !== null) {
                        return number_format($value->getPrice(), 2, '.', '') . ' €';
                    } else {
                        return ''; 
                    }
                }),

                AssociationField::new('article', 'Articles')
                ->formatValue(function ($value, $entity) {
                    if ($value !== null) {
                        return $value->getName();
                    } else {
                        return ''; 
                    }
                }),
            
            AssociationField::new('article', 'Prix HT')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if ($value !== null) {
                        return number_format($value->getPrice(), 2, '.', '') . ' €';
                    } else {
                        return ''; 
                    }
                }),
            
            AssociationField::new('State', 'Statut')
            ->formatValue(function ($value, $entity) {
                return $value ? $value->getName() : null;
            }),

            AssociationField::new('material')
            ->formatValue(function ($value, $entity) {
                return $value ? $value->getName() : null;
            }),
            AssociationField::new('material', 'Prix')
            ->formatValue(function ($value, $entity) {
                return $value ? number_format($value->getPrice(), 2, '.', '') . ' €' : null;
            })
            ->onlyOnIndex(),

            IntegerField::new('quantity'),
            
            NumberField::new('price_ht', 'Total HT')
            ->formatValue(function ($value, $entity) {
                return number_format($entity->calculateTotalHT(), 2, ',', '') . ' €';
            })
            ->onlyOnIndex(),

            AssociationField::new('orderlinecommand', 'n° Order')
            ->formatValue(function ($value, $entity) {
                return $value ? $value->getId() : '';
            })
            ->autocomplete()
            ->setRequired(true),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isSuperAdmin = $this->security->isGranted('ROLE_SUPER_ADMIN');
        $isEmployee = $this->security->isGranted('ROLE_EMPLOYEE');
        
        $actionsConfig = $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW);

        if ($isEmployee) {
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::EDIT);
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::DELETE);
        }

        return $actionsConfig;
    }
}




