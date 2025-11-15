<?php

namespace App\Controller\Admin;

use App\Entity\Galerie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SearchMode;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GalerieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Galerie::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setSearchFields(['name', 'description'])
            ->setAutofocusSearch()
            ->setSearchMode(SearchMode::ALL_TERMS)
            ->setPaginatorPageSize(12)
            ->setPaginatorRangeSize(2)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true)
            // tri par défaut : d’abord position, puis date
            ->setDefaultSort([
                'position'  => 'ASC',
                'createdAt' => 'DESC',
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm()
                ->hideOnDetail()
                ->hideOnIndex(),

            TextField::new('name', 'Nom'),

            IntegerField::new('position', 'Ordre')
                ->setHelp('Plus le nombre est petit, plus la photo apparaît en haut.')
                ->setFormTypeOption('attr', ['style' => 'max-width: 100px;']),

            ImageField::new('featuredImage', 'Image')
                ->setBasePath('/images/uploads/')
                ->setUploadDir('public/images/uploads/'),

            DateTimeField::new('createdAt', 'Créée le')
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        // Action pour MONTER la photo
        $moveUp = Action::new('moveUp', '')
            ->setIcon('fa fa-arrow-up')
            ->setLabel(false)
            ->setCssClass('btn btn-link text-secondary p-0')
            ->setHtmlAttributes(['title' => 'Monter'])
            ->linkToCrudAction('moveUp');

        // Action pour DESCENDRE la photo
        $moveDown = Action::new('moveDown', '')
            ->setIcon('fa fa-arrow-down')
            ->setLabel(false)
            ->setCssClass('btn btn-link text-secondary p-0')
            ->setHtmlAttributes(['title' => 'Descendre'])
            ->linkToCrudAction('moveDown');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            // si tu veux empêcher l’édition via index, décommente la ligne suivante :
            // ->remove(Crud::PAGE_INDEX, Action::EDIT)

            // 👉 on ajoute les flèches sur chaque LIGNE de l’index
            ->add(Crud::PAGE_INDEX, $moveUp)
            ->add(Crud::PAGE_INDEX, $moveDown);
    }

    /**
     * Monter la photo dans l'ordre (échanger la position avec la précédente).
     */
    public function moveUp(
        Request $request,
        EntityManagerInterface $em,
        AdminUrlGenerator $urlGenerator
    ): Response {
        $id = $request->query->get('entityId');

        if (!$id) {
            $this->addFlash('warning', 'Aucune photo sélectionnée.');
            return $this->redirectToIndex($urlGenerator);
        }

        /** @var Galerie|null $galerie */
        $galerie = $em->getRepository(Galerie::class)->find($id);

        if (!$galerie) {
            $this->addFlash('warning', 'Photo introuvable.');
            return $this->redirectToIndex($urlGenerator);
        }

        $currentPos = $galerie->getPosition() ?? 0;
        $repo = $em->getRepository(Galerie::class);

        // Élément précédent = position PLUS PETITE la plus proche
        $previous = $repo->createQueryBuilder('g')
            ->andWhere('g.position < :pos')
            ->setParameter('pos', $currentPos)
            ->orderBy('g.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($previous instanceof Galerie) {
            $prevPos = $previous->getPosition();
            $previous->setPosition($currentPos);
            $galerie->setPosition($prevPos);
            $em->flush();
        }

        return $this->redirectToIndex($urlGenerator);
    }

    /**
     * Descendre la photo dans l'ordre (échanger la position avec la suivante).
     */
    public function moveDown(
        Request $request,
        EntityManagerInterface $em,
        AdminUrlGenerator $urlGenerator
    ): Response {
        $id = $request->query->get('entityId');

        if (!$id) {
            $this->addFlash('warning', 'Aucune photo sélectionnée.');
            return $this->redirectToIndex($urlGenerator);
        }

        /** @var Galerie|null $galerie */
        $galerie = $em->getRepository(Galerie::class)->find($id);

        if (!$galerie) {
            $this->addFlash('warning', 'Photo introuvable.');
            return $this->redirectToIndex($urlGenerator);
        }

        $currentPos = $galerie->getPosition() ?? 0;
        $repo = $em->getRepository(Galerie::class);

        // Élément suivant = position PLUS GRANDE la plus proche
        $next = $repo->createQueryBuilder('g')
            ->andWhere('g.position > :pos')
            ->setParameter('pos', $currentPos)
            ->orderBy('g.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($next instanceof Galerie) {
            $nextPos = $next->getPosition();
            $next->setPosition($currentPos);
            $galerie->setPosition($nextPos);
            $em->flush();
        }

        return $this->redirectToIndex($urlGenerator);
    }

    private function redirectToIndex(AdminUrlGenerator $urlGenerator): RedirectResponse
    {
        $url = $urlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return new RedirectResponse($url);
    }
}
