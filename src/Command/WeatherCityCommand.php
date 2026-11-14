<?php
namespace App\Command;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Pobieranie prognozy pogody dla miasta',
)]
class WeatherCityCommand
{
    public function __construct(
        private WeatherUtil $weatherUtil
    ) {}
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country Code')] string $countryCode,
        #[Argument('City')] string $city,
    ): int {
        $forecasts = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $city);

        $io->writeln(sprintf('Location: %s, %s', $city, $countryCode));

        $io->table(['Date', 'Temperature', 'Precipitation', 'Wind', 'Humidity'],
            array_map(fn($f) => [
                $f->getDate()->format('Y-m-d'),
                $f->getTemperature(),
                $f->getPrecipitation(),
                $f->getWind(),
                $f->getHumidity(),
            ], $forecasts)
        );
        return Command::SUCCESS;
    }
}
