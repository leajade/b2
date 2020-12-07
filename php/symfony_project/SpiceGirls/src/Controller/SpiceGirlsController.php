<?php

namespace App\Controller;

use App\Entity\Offers;
use App\Entity\KindsContracts;
use App\Entity\TypesContracts;
use App\Form\OfferFormType;
use App\Repository\KindsContractsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\OffersRepository;
use App\Repository\TypesContractsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpiceGirlsController extends AbstractController
{
    /**
     * @Route("/", name="spice")
     * @param OffersRepository $offersRepository
     */

    public function index(OffersRepository $offersRepository)
    {
        $offers = $offersRepository->findAll();
        return $this->render('spice/index.html.twig', [
            'offers' => $offers,
        ]);
    }

    /**
     * @Route("/posts/{id}", name="post")
     * @param Offers $offers
     */
    public function offer(Offers $offer, Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('spice/offer.html.twig', [
            'offer' => $offer,

        ]);
    }

    /**
     * @Route("/add", name="add")
     * @IsGranted("ROLE_USER")
     * @param Offers $offers
     */

    public function add(Request $request, EntityManagerInterface $entityManager)
    {

        $offernew = new Offers();
        $form = $this->createForm(OfferFormType::class, $offernew);

        $offernew->setCreationDate(new \DateTime());
        $offernew->setUpdateDate(new \DateTime());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($offernew);
            $entityManager->flush();

            $this->addFlash('success', 'Article added!');

            return $this->redirectToRoute('spice');
        }

        return $this->render('spice/add.html.twig', [
            "form" => $form->createView()

        ]);

    }

    /**
     * @Route("/posts/{id}/update", name="updateOffer")
     * @param Offers $offers
     */

    public function edit(Offers $offer, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(OfferFormType::class, $offer);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $offer = $form->getData();

            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Article Updated!');

            return $this->redirectToRoute('spice');
        }

        return $this->render('spice/updateOffer.html.twig', [
            "form" => $form->createView()

        ]);
    }
}

