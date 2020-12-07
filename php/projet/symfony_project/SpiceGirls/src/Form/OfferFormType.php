<?php

namespace App\Form;

use App\Entity\KindsContracts;
use App\Entity\Offers;
use App\Entity\TypesContracts;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("Title",  TextType::class, [
                "attr" => ["class" => "form-control"]])

            ->add("Description", TextareaType::class, [
                "attr" => ["class" => "form-control"]])

            ->add("Address",  TextType::class, [
                "attr" => ["class" => "form-control"]])

            ->add("ZipCode",  TextType::class, [
                "attr" => ["class" => "form-control"]])

            ->add("City",  TextType::class, [
                "attr" => ["class" => "form-control"]])

            ->add("KindContract", EntityType::class, [
                'class' => KindsContracts::class,
                'choice_label' => 'title',
                "attr" => ["class" => "form-control"]])

            ->add("typesContracts", EntityType::class, [
                'class' => TypesContracts::class,
                'choice_label' => 'title',
                "attr" => ["class" => "form-control"]])

            ->add("EndContract", DateType::class, [
                "attr" => ["class" => "my-2"]])

            ->add("submit", SubmitType::class, [
                "attr" => ["class" => "btn btn-primary mt-2"]])
            ->getForm();


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offers::class,
        ]);
    }
}
