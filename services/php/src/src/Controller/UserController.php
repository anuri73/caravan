<?php

namespace App\Controller;

use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Model\RegistrationModel;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class UserController extends AbstractFOSRestController
{
    private UserPasswordHasherInterface $passwordHashService;
    private UserDataProvider $dataProvider;

    public function __construct(
        UserPasswordHasherInterface $passwordHashService,
        UserDataProvider            $dataProvider
    )
    {
        $this->passwordHashService = $passwordHashService;
        $this->dataProvider = $dataProvider;
    }

    public function register(Request $request)
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            if (!($formData instanceof RegistrationModel)) {
                throw new InvalidArgumentException('Invalid instance of form data');
            }
            $user = new User();
            $user->setEmail($formData->email);
            $user->setPasswordHash(
                $this->passwordHashService->hashPassword($user, $formData->password)
            );
            $this->dataProvider->add($user);

            return $this->json(
                $user,
                Response::HTTP_OK,
                [],
                [
                    AbstractNormalizer::GROUPS => 'user_registration',
                    AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true
                ]
            );
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = [
                'field' => $error->getOrigin()->getName(),
                'message' => $error->getMessage(),
            ];
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}