<?php
namespace App\Controller;
use App\Entity\Forecast;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        WeatherUtil $weatherUtil,
        #[MapQueryParameter] string $city,
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false
    ): Response
    {
        $measurements = $weatherUtil->getWeatherForCountryAndCity(
            $country,
            $city
        );

        if ($twig) {
            $template = $format === 'csv'
                ? 'weather_api/index.csv.twig'
                : 'weather_api/index.json.twig';

            $response = $this->render($template, [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);

            $response->headers->set(
                'Content-Type',
                $format === 'csv'
                    ? 'text/csv; charset=UTF-8'
                    : 'application/json; charset=UTF-8'
            );

            return $response;
        }

        if ($format === 'csv') {
            $rows = [];
            $rows[] = 'city,country,date,celsius,fahrenheit';

            foreach ($measurements as $m) {
                $rows[] = implode(',', [
                    $city,
                    $country,
                    $m->getDate()->format('Y-m-d'),
                    $m->getTemperature(),
                    $m->getFahrenheit(),
                ]);
            }

            $csv = implode("\r\n", $rows) . "\r\n";

            return new Response(
                $csv,
                200,
                ['Content-Type' => 'text/csv; charset=UTF-8']
            );
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(
                fn(Forecast $m) => [
                    'date' => $m->getDate()->format('Y-m-d'),
                    'celsius' => $m->getTemperature(),
                    'fahrenheit' => $m->getFahrenheit(),
                    'precipitation' => $m->getPrecipitation(),
                    'wind' => $m->getWind(),
                    'humidity' => $m->getHumidity(),
                ],
                $measurements
            ),
        ]);
    }
}
