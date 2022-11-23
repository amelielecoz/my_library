<?php

namespace App\Controller\Admin;

use App\Entity\ReservationRequest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ReservationRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReservationRequest::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field::new('requestorName');
        yield AssociationField::new('book');
        yield DateTimeField::new('createdAt');
    }

}
