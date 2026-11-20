<?php
namespace App\Tests\Entity;
use App\Entity\Forecast;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
class MeasurementTest extends TestCase
{
    public static function dataGetFahrenheit(): array
    {
        return [
            ['0', '32'],
            ['-100', '-148'],
            ['100', '212'],
            ['13.5', '56.3'],
            ['-27.5', '-17.5'],
            ['46', '114.8'],
            ['-8.5', '16.7'],
            ['73.5', '164.3'],
            ['19', '66.2'],
            ['0.5', '32.9'],
        ];
    }

    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(
        string $celsius,
        string $expectedFahrenheit
    ): void
    {
        $measurement = new Forecast();

        $measurement->setTemperature((float) $celsius);

        $this->assertEquals((float) $expectedFahrenheit, $measurement->getFahrenheit());
    }
}
