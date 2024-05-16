<?php

namespace App\Form;

use App\Entity\Produit;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, SubmitType};

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;

class ProduitType extends AbstractType
{   

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->entityManager->getRepository(Categorie::class)->findAll();

        $builder
            ->add('Nom')
            ->add('Description')
            ->add('Prix')
            ->add('Qtte_stock')
            ->add('Qtte_seuil_min')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'Description',
            ])
            ->add('ajouter', SubmitType::class)
        ;

    }

        



}
