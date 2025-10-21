<?php

namespace App\Controller\Admin;

use App\Controller\Noustrouver;
use App\Entity\Contact;
use App\Entity\Galerie;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    

    public function index(): Response
    {
        
      return $this->render('admin/dashboard/index.html.twig', [
        'controller_name' => 'DashboardController'
      ]);
      
    }

    
        
    

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Bsm Laiguizeure Fr')
            ->setTitle('<img src="/images/marco02.png" alt="logo Marco l\'aiguiseur" width="150" height="130">');
            

    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->renderContentMaximized()
            ->showEntityActionsInlined();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('home', 'fa-solid fa-house', 'app_home');
        
        
        yield MenuItem::linkToUrl('bsm-laiguizeure', 'fa-solid fa-earth-americas', 'https://bsm-laiguizeure.fr' );

        yield MenuItem::section('contact');
        yield MenuItem::linkToCrud('email', 'fa-solid fa-address-card', Contact::class);

        yield MenuItem::section('galerie');
        yield MenuItem::linkToCrud('photo', 'fa-solid fa-address-card', Galerie::class);
        
        
        
        yield MenuItem::section('');
        yield MenuItem::linkToLogout('déconnexion', 'fa-solid fa-right-to-bracket');
        
        
    }

    
    
}
