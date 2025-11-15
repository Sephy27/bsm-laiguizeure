<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Entity\Galerie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function index(): Response
    {
        $contactRepo = $this->em->getRepository(Contact::class);
        $galerieRepo = $this->em->getRepository(Galerie::class);

        // 1) Construire la frise des 6 derniers mois (clé = "Y-m")
        $now = new \DateTimeImmutable('first day of this month 00:00:00');

        $months = []; // ex: ['2025-05' => ['label' => 'Mai 2025', 'contacts' => 0, 'photos' => 0], ...]
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->sub(new \DateInterval('P'.$i.'M'));
            $key   = $month->format('Y-m');

            // Pour un label lisible (ex: "Novembre 2025")
            $label = \IntlDateFormatter::formatObject(
                $month,
                'MMMM yyyy',
                'fr_FR'
            );

            $months[$key] = [
                'label'    => ucfirst($label),
                'contacts' => 0,
                'photos'   => 0,
            ];
        }

        // 2) Date mini pour les requêtes (début du plus ancien mois)
        $firstKey  = array_key_first($months);
        $fromDate  = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $firstKey.'-01 00:00:00');

        // 3) Récupérer tous les contacts depuis cette date
        $contacts = $contactRepo->createQueryBuilder('c')
            ->where('c.createdAt >= :from')
            ->setParameter('from', $fromDate)
            ->getQuery()
            ->getResult();

        foreach ($contacts as $contact) {
            /** @var Contact $contact */
            if (!$contact->getCreatedAt()) {
                continue;
            }
            $key = $contact->getCreatedAt()->format('Y-m');
            if (isset($months[$key])) {
                $months[$key]['contacts']++;
            }
        }

        // 4) Récupérer les photos depuis cette date
        $photos = $galerieRepo->createQueryBuilder('g')
            ->where('g.createdAt >= :from')
            ->setParameter('from', $fromDate)
            ->getQuery()
            ->getResult();

        foreach ($photos as $photo) {
            /** @var Galerie $photo */
            // ⚠️ adapte le nom du champ date si besoin (createdAt, uploadedAt, etc.)
            if (!method_exists($photo, 'getCreatedAt') || !$photo->getCreatedAt()) {
                continue;
            }
            $key = $photo->getCreatedAt()->format('Y-m');
            if (isset($months[$key])) {
                $months[$key]['photos']++;
            }
        }

        // 5) Extraire les tableaux pour le JS
        $chartLabels       = array_column($months, 'label');       // ["Juin 2025", ...]
        $chartContacts     = array_column($months, 'contacts');    // [3, 5, ...]
        $chartPhotos       = array_column($months, 'photos');      // [1, 0, ...]

        // 6) Stats globales + ce mois-ci
        $contactsTotal     = $contactRepo->count([]);
        $photosTotal       = $galerieRepo->count([]);

        $lastKey           = array_key_last($months);
        $contactsThisMonth = $months[$lastKey]['contacts'];
        $photosThisMonth   = $months[$lastKey]['photos'];

        return $this->render('admin/index.html.twig', [
            'contactsCount'      => $contactsTotal,
            'photosCount'        => $photosTotal,
            'contactsThisMonth'  => $contactsThisMonth,
            'photosThisMonth'    => $photosThisMonth,
            'lastContacts'       => $contactRepo->findBy([], ['createdAt' => 'DESC'], 5),

            // Données pour le graphique
            'chartLabels'        => $chartLabels,
            'chartContacts'      => $chartContacts,
            'chartPhotos'        => $chartPhotos,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/marcologo04.png" width="140">')
            ->setFaviconPath('favicon.svg')
            ->renderContentMaximized()
            ->setTranslationDomain('admin');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->renderContentMaximized()
            ->showEntityActionsInlined();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-globe', 'app_home');
        yield MenuItem::linkToRoute('Tester le formulaire', 'fa fa-envelope', 'app_contact');

        yield MenuItem::section('Contacts');
        yield MenuItem::linkToCrud('Emails reçus', 'fa fa-envelope-open', Contact::class);

        yield MenuItem::section('Galerie');
        yield MenuItem::linkToCrud('Photos', 'fa fa-camera', Galerie::class);

        yield MenuItem::section();
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-right-from-bracket');
    }
}
