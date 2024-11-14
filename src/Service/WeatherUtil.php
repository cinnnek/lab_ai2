<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\MeasurementRepository;
use App\Repository\LocationRepository;

class WeatherUtil
{
    private MeasurementRepository $measurementRepository;
    private LocationRepository $locationRepository;

    public function __construct(
        MeasurementRepository $measurementRepository,
        LocationRepository $locationRepository
    ) {
        $this->measurementRepository = $measurementRepository;
        $this->locationRepository = $locationRepository;
    }

    public function getWeatherForLocation(Location $location): array
    {
        return $this->measurementRepository->findByLocation($location);
    }

    /**
     * @return Measurement[]
     */
    public function getWeatherForCountryAndCity(string $countryCode, string $city): array
    {
        $location = $this->locationRepository->findBy([
            'country' => $countryCode,
            'name' => $city
        ])[0];
        return $this->getWeatherForLocation($location);
    }
}
