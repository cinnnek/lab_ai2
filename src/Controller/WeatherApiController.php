<?php

namespace App\Controller;

use App\Service\WeatherUtil;
use App\Entity\Measurement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'api_weather', methods: ['GET'])]
    public function index(
        #[MapQueryParameter('city')] string    $city,
        #[MapQueryParameter('country')] string $country,
        WeatherUtil $weatherUtil,
        #[MapQueryParameter('format')] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false
    ): Response
    {
        $location = $weatherUtil->getWeatherForCountryAndCity($country, $city);
        $measurements = $weatherUtil->getWeatherForLocation($location);

        if ($format === 'csv') {
            if ($twig) {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
            $csvData = "city,country,date,temperature,fahrenheit\n";
            foreach ($measurements as $measurement) {
                $csvData .= sprintf (
                    "%s,%s,%s,%s,%s\n",
                    $city,
                    $country,
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius(),
                    $measurement->getFahrenheit()
                );
            }
            $response = new Response($csvData);
            $response->headers->set('Content-Type', 'text/csv');
            return $response;
        }
        if ($twig){
            return $this->render('weather_api/index.json.twig', [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);
        }
        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn($m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(),
            ], $measurements)
        ]);
    }
}
