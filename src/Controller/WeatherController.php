<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\ForecastRepository;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

final class WeatherController extends AbstractController
{
    #[Route('/weather/{country}/{city}', name: 'app_weather')]
    public function city(#[MapEntity(mapping: ['city' => 'name', 'country' => 'country'])] Location $location,
                         #ForecastRepository $repository
                        WeatherUtil $util): Response
    {
        #$forecasts = $repository->findByLocation($location);
        $forecasts = $util->getWeatherForLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'forecasts' => $forecasts,
        ]);
    }

}
