<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        $assignedToYou = $this->em->getRepository(Activity::class)->findBy(['assignedTo' => $this->getUser()]);
        $yourActivities = $this->em->getRepository(Activity::class)->findBy(['createdBy' => $this->getUser()]);
        return $this->render('dashboard/dashboard.html.twig', [
            'controller_name' => 'Dashboard',
            'assignedToYou' => $assignedToYou,
            'yourActivities' => $yourActivities,
        ]);
    }

    /**
     * @Route("/activity/save/{id}", name="save-activity")
     */
    public function saveActivity($id, Request $request): Response
    {
        $activity = $this->em->getRepository(Activity::class)->find($id);

        $form = $this->createForm(ActivityType::class, $activity);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($activity);
            $this->em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/activity-save.html.twig', [
            'controller_name' => 'Edit activity',
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/activity/delete/{id}", name="delete-activity")
     */
    public function deleteActivity($id): Response
    {
        $activity = $this->em->getRepository(Activity::class)->find($id);
        if($activity->getCreatedBy() !== $this->getUser()) {
            $this->addFlash(
                'warning',
                'You did not create this activity!'
            );
            return $this->redirectToRoute('dashboard');
        }
        $this->em->remove($activity);
        $this->em->flush();
        $this->addFlash(
            'success',
            'Activity removed!'
        );
        return $this->redirectToRoute('dashboard');
    }
}
