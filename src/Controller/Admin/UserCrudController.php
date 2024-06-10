<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;


class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;
    private $security;

    public function __construct(UserPasswordHasherInterface $passwordHasher, Security $security)
    {
        $this->passwordHasher = $passwordHasher;
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->renderAsBadges([
                    'ROLE_ADMIN' => 'success',
                    'ROLE_SUPER_ADMIN' => 'success',
                    'ROLE_EMPLOYEE' => 'primary',
                    'ROLE_USER' => 'warning',
                ])
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                    'Super-Admin' => 'ROLE_SUPER_ADMIN',
                    'Employee' => 'ROLE_EMPLOYEE',
                    'User' => 'ROLE_USER'
                ]),
            TextField::new('gender', 'Genre'),
            TextField::new('lastname', 'Nom'),
            TextField::new('firstname', 'Prénom'),
            EmailField::new('email'),
            TextField::new('password')
                ->onlyOnForms(),
            DateField::new('birthday', 'Date de naissance')
                ->setFormat('dd/MM/yyyy'),
            TextField::new('address', 'Adresse Postale'),
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

        if ($isAdmin) {
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::DELETE);
        }

        if ($isEmployee) {
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::EDIT);
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::DELETE);
        }

        return $actionsConfig;
    }
    
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword()));
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}