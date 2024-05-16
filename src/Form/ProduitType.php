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
            ->add('idCategorie', ChoiceType::class, [
                'choices' => $categories,
                'choice_label' => 'description'
            ])
            ->add('ajouter', SubmitType::class)
        ;

        $builder->get('idCategorie')
        ->addModelTransformer(new CallbackTransformer(
            function ($categorieAsEntity) {
                // transform the Categorie entity to its id
                return $categorieAsEntity ? $categorieAsEntity->getId() : null;
            },
            function ($categorieAsId) {
                // transform the id back to a Categorie entity
                // this is not needed if 'idCategorie' is a write-only property
                return $categorieAsId ? $this->entityManager->getRepository(Categorie::class)->find($categorieAsId) : null;
            }
        ));
        

    }

        



}
