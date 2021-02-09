<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Services\MailService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    public function forgetPassword(Request $request, UserRepository $userRepo, EntityManagerInterface $em, MailService $mailService,
     SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        try {
            $resetPassword = $serializer->deserialize($request->getContent(), ResetPassword::class, 'json', ['groups' => 'forget-password']);
        } catch (NotEncodableValueException $e) {
            return $this->jsonData('The JSON format is invalid.', 400);
        }
       
        $errors = [];

        foreach ($validator->validate($resetPassword, null, ['forget-password']) as $error) {
            $errors[] = [
                'error' => $error->getMessage(),
                'field' => $error->getPropertyPath(),
            ];
        }

        if (count($errors) > 0) {
            return $this->jsonData($errors, 400);
        }

        $user = $userRepo->findOneBy(['email' => $resetPassword->getEmail()]);

        if (!$user) {
            return $this->jsonData('User Not Found.', 404);
        }

        try {
            $code = $mailService->sendCode($resetPassword->getEmail(), 'Reset Password Request', 'emails/forget_password.html.twig');
            $user->setConfirmationCode($code)
                ->setCodeDate(new \DateTime())
            ;
            $em->persist($user);
            $em->flush();

            return $this->jsonData($code/*'An email sent Check it to reset your password.'*/, 200);
        } catch (\Throwable $th) {
            return $this->jsonData('Unexpected Server Error.', 500);
        }
    }

    public function resetPassword(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserRepository $userRepo,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $resetPassword = $serializer->deserialize($request->getContent(), ResetPassword::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->jsonData('The JSON format is invalid.', 400);
        }
       
        $errors = [];

        foreach ($validator->validate($resetPassword) as $error) {
            $errors[] = [
                'error' => $error->getMessage(),
                'field' => $error->getPropertyPath(),
            ];
        }

        if (count($errors) > 0) {
            return $this->jsonData($errors, 400);
        }

        $user = $userRepo->findOneBy(['confirmationCode' => $resetPassword->getConfirmationCode()]);
       
        if (!$user) {
            return $this->jsonData('User Not Found', 404);
        }

        try {
            $user->setPassword($encoder->encodePassword($user, $resetPassword->getPassword()))
                ->setConfirmationCode(null)
                ->setCodeDate(null)
            ;
            $em->persist($user);
            $em->flush();
            return $this->jsonData('Password Updated Successfully', 200);
        } catch (\Throwable $th) {
            return $this->jsonData('Unexpected Server Error', 500);
        }
    }

    private function jsonData($msg, int $status): JsonResponse
    {
        return $this->json([
            'msg' => $msg,
            'status' => $status,
        ], $status);
    }
}
