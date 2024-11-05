<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Location;
use App\Repository\MeasurementRepository;
use App\Repository\LocationRepository;



class WeatherController extends AbstractController
{
    #[Route('/weather/{city}', name: 'app_weather_by_city')]
    public function city(string $city, LocationRepository $locationRepository, MeasurementRepository $measurementRepository): Response
    {

        $location = $locationRepository->findOneBy(['city' => $city]);

        if (!$location) {
            throw $this->createNotFoundException("Location not found for city: $city");
        }


        $measurements = $measurementRepository->findByLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
