<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud);
    }
    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm()
            ->hideOnDetail()
            ->hideOnIndex();
        yield TextField::new('fullName', 'Nom / Prénom');
        yield EmailField::new('email', 'Email');
        yield TextField::new('subject', 'Sujet');
        yield TextField::new('message', 'Message');
        yield DateTimeField::new('createdAt', 'Crée le');
        yield ImageField::new('featuredImage', 'Image')
            ->setBasePath('images/uploads/')
            ->setUploadDir('images/uploads/');

        
    }
    
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ;
    }
}
