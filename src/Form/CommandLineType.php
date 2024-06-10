<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\CommandLine;
use App\Entity\Material;
use App\Entity\Order;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver; 
use \Symfony\Component\Form\Extension\Core\Type\IntegerType;


class CommandLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priceHT')
            ->add('priceTTC')
            ->add('quantity', IntegerType::class)
            ->add('state')
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
            ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'name',
            ])
            ->add('orderlinecommand', EntityType::class, [
                'class' => Order::class,
                'choice_label' => 'id',
            ])
            ->add('material', EntityType::class, [
                'class' => Material::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommandLine::class,
        ]);
    }
}
