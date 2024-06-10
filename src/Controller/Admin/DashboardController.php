<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\CommandLine;
use App\Entity\Material;
use App\Entity\Order;
use App\Entity\Service;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(OrderCrudController::class)->generateUrl());    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="./assets/images/Logo.png" style="max-width:150px; width:100%;">')
            ->setFaviconPath('./assets/images/logo_short.svg');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home' );

        yield MenuItem::section('Gestion Utilisateurs');
        $submenuItems = [
            MenuItem::linkToCrud('Liste Users', 'fas fa-eye', User::class),
        ];
        if ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_ADMIN')) {
            $submenuItems[] = MenuItem::linkToCrud('Créer User', 'fas fa-plus', User::class)
                ->setAction(Crud::PAGE_NEW);
        }
        yield MenuItem::subMenu('User', 'fa-solid fa-user-tag')->setSubItems($submenuItems);
        

        yield MenuItem::section('Gestion des commandes');
        $submenuItemsOrders = [
            MenuItem::linkToCrud('Liste Commandes', 'fas fa-eye', Order::class),
        ];
        if ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_ADMIN')) {
            $submenuItemsOrders[] = MenuItem::linkToCrud('Créer Commande', 'fas fa-plus', Order::class)
                ->setAction(Crud::PAGE_NEW);
        }
        yield MenuItem::subMenu('Commandes', 'fa-solid fa-basket-shopping')->setSubItems($submenuItemsOrders);
        
        $submenuItemsCommandLines = [
            MenuItem::linkToCrud('Liste Ligne de Commandes', 'fas fa-eye', CommandLine::class),
        ];
        if ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_ADMIN')) {
            $submenuItemsCommandLines[] = MenuItem::linkToCrud('Créer Ligne de Commande', 'fas fa-plus', CommandLine::class)
                ->setAction(Crud::PAGE_NEW);
        }
        yield MenuItem::subMenu('Ligne de Commandes', 'fa-solid fa-basket-shopping')->setSubItems($submenuItemsCommandLines);
        
        yield MenuItem::section('Contenu Service');

        yield MenuItem::subMenu('Prestations', 'fa-solid fa-water')->setSubItems([
            MenuItem::linkToCrud('Liste Pretations', 'fas fa-eye',Service::class),
            MenuItem::linkToCrud('Créer Pretations', 'fas fa-plus',Service::class)
                ->setAction(Crud::PAGE_NEW)
                ->setPermission('ROLE_SUPER_ADMIN'),
        ]);

        yield MenuItem::subMenu('Articles', 'fa-solid fa-shirt')->setSubItems([
            MenuItem::linkToCrud('Liste Articles', 'fas fa-eye',Article::class),
            MenuItem::linkToCrud('Créer Articles', 'fas fa-plus',Article::class)
                ->setAction(Crud::PAGE_NEW)
                ->setPermission('ROLE_SUPER_ADMIN'),
        ]);

        yield MenuItem::subMenu('Categories', 'fa-solid fa-square-minus')->setSubItems([
            MenuItem::linkToCrud('Liste Categories', 'fas fa-eye',Category::class),
            MenuItem::linkToCrud('Créer Categories', 'fas fa-plus',Category::class)
                ->setAction(Crud::PAGE_NEW)
                ->setPermission('ROLE_SUPER_ADMIN'),
        ]);

        yield MenuItem::subMenu('Matières', 'fa-solid fa-feather')->setSubItems([
            MenuItem::linkToCrud('Liste Matières', 'fas fa-eye',Material::class),
            MenuItem::linkToCrud('Créer Matières', 'fas fa-plus',Material::class)
                ->setAction(Crud::PAGE_NEW)
                ->setPermission('ROLE_SUPER_ADMIN'),
        ]);
    }

    // public function configureUserMenu(UserInterface $user): UserMenu {
    //     return parent::configureUserMenu($user);
    // }
}
