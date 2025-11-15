<?php

namespace App\Controller\Admin;

use App\Entity\Galerie;
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
            // 👉 tri par défaut : d’abord position, puis date
            ->setDefaultSort([
                'position' => 'ASC',
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

            // 👉 Champ d’ordre
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
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            
            ;
    }
}

