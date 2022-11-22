<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field::new('slug')->setDisabled();
        yield Field::new('title');
        yield Field::new('isbn');
        yield Field::new('isbn13');
        yield TextareaField::new('summary');
        yield AssociationField::new('authors')
            ->autocomplete()
            ->setFormTypeOption('by_reference', false);
        yield BooleanField::new('isAvailable')->renderAsSwitch();
    }
}
