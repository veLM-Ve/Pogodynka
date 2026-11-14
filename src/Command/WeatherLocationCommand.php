<?php
namespace App\Command;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\LocationRepository;
use App\Service\WeatherUtil;

#[AsCommand(
    name: 'weather:location',
    description: 'Pobieranie prognozy pogody dla lokalizacji',
)]
class WeatherLocationCommand{
    public function __construct(
        private LocationRepository $locationRepository,
        private WeatherUtil $weatherUtil
    ) {
    }
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Location ID')] int $id,
    ): int {
        $location = $this->locationRepository->find($id);

        $forecasts = $this->weatherUtil->getWeatherForLocation($location);
        $io->writeln(sprintf('Location: %s', $location->getName()));
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
