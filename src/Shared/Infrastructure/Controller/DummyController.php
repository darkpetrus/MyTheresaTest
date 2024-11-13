<?php
namespace App\Shared\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class DummyController extends AbstractController
{
    function index(): JsonResponse
    {
        return new JsonResponse(
            [
                'dummy' => true,
            ]
        );
    }
}