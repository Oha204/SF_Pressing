<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\CommandLineType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\UserRepository;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $security;
    private $userrepo;

    public function __construct(EntityManagerInterface $entityManager, Security $security, UserRepository $userrepo)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->userrepo = $userrepo;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $employechoice = $this->userrepo->findEmployees(); 
        $usersWithRoleUser = $this->userrepo->findUsersWithRoleUser();


        return [
            IdField::new('id')
                ->onlyOnIndex(),
            
            ChoiceField::new('state', 'Statut')
                ->setRequired(true)
                ->renderAsBadges([
                    'Non-traitée' => 'danger',
                    'En cours' => 'warning',
                    'Terminé' => 'info',
                    'Récupérée' => 'success',
                ])
                ->setChoices([
                    'Non-traitée' => 'Non-traitée',
                    'En cours' => 'En cours',
                    'Terminée' => 'Terminé',
                    'Récupérée' => 'Récupérée'
                ]),

            AssociationField::new('employee', 'Employé assigné')
                ->formatValue(function ($value, $entity) {
                    if ($value) {
                        return $value->getFirstname();
                    } else {
                        return null;
                    }
                })
                ->hideOnForm(),

                AssociationField::new('employee', 'Employé assigné')
                ->setFormTypeOptions(['choices' => $employechoice])
                ->onlyOnForms(),

            // AssociationField::new('employee', 'Employé assigné')
            //     ->autocomplete()
            //     ->formatValue(function ($value, $entity) {
            //             return $value->getFirstname();
            //         })
            //     ->onlyOnForms(),

            AssociationField::new('client', 'Numéro client')
                ->formatValue(function ($value, $entity) {
                    if ($value) {
                        return $value->getId();
                    } else {
                        return null;
                    }
                })
                ->onlyOnIndex(),

                AssociationField::new('client', 'Numéro client')
                ->formatValue(function ($value, $entity) {
                    if ($value) {
                        return $value->getFirstname() . ' ' . $value->getLastname();
                    } else {
                        return null;
                    }
                })
                ->onlyOnDetail(),

            AssociationField::new('client', 'Numéro client')
                ->onlyWhenCreating()
                ->setRequired(true)
                ->setFormTypeOptions(['choices' => $usersWithRoleUser]),

            NumberField::new('total_price_ht', 'Total HT')
                ->formatValue(function ($value, $entity) {
                    return number_format($entity->calculateTotalHT(), 2, ',', '') . ' €';
                })
                ->hideOnForm(),
            NumberField::new('total_price_ttc', 'Total TTC')
                ->formatValue(function ($value, $entity) {
                    return number_format($entity->calculateTotalTTC(), 2, ',', '') . ' €';
                })
                ->hideOnForm(),

            DateField::new('payment_date', 'Date de paiement')
                ->setFormat('dd/MM/yyyy')
                ->hideWhenUpdating(),
            DateField::new('deposit_date', 'Date de dépôt')
                ->setFormat('dd/MM/yyyy'),
            DateField::new('pickup_date', 'Date de retrait')
                ->setFormat('dd/MM/yyyy'),

            AssociationField::new('commandLines', 'Order Content')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    return implode(', ', array_map(function ($commandLine) {
                        return $commandLine->getId();
                    }, $value->toArray()));
                }),

                CollectionField::new('commandLines', 'Contenu de la commande')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    $formattedLines = [];
                    foreach ($value as $commandLine) {
                        $formattedLine = "Ligne de commande #" . $commandLine->getId() . " : <br>";
                        $formattedLine .= "• Service : " . $commandLine->getService()->getName() . " <br>";
                        $formattedLine .= "• Article & Qté : " . $commandLine->getArticle()->getName() . " x " . $commandLine->getQuantity() . " <br>";
                        $formattedLine .= "• Matière : " . ($commandLine->getMaterial() ? $commandLine->getMaterial()->getName() : "N/A") . " <br>";
                        $formattedLine .= "• Etat : " . ($commandLine->getState() ? $commandLine->getState()->getName() : "N/A") . " <br>";
                        $formattedLine .= "• Prix total HT : " . number_format($commandLine->calculateTotalHT(), 2, ',', '') . " € <br>";
                        $formattedLines[] = $formattedLine;
                        $formattedLines[] = "<br>";
                    }
                    return implode('', $formattedLines);
                }),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isSuperAdmin = $this->security->isGranted('ROLE_SUPER_ADMIN');
        $isEmployee = $this->security->isGranted('ROLE_EMPLOYEE');

        $actionsConfig = $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW);

        if ($isEmployee) {
            // $actionsConfig->remove(Crud::PAGE_INDEX, Action::EDIT);
            $actionsConfig->remove(Crud::PAGE_DETAIL, Action::EDIT);
            $actionsConfig->remove(Crud::PAGE_DETAIL, Action::DELETE);
            $actionsConfig->remove(Crud::PAGE_INDEX, Action::DELETE);
        }

        return $actionsConfig;
    }

    

}
