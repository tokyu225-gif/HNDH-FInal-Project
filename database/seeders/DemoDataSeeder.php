<?php

namespace Database\Seeders;

use App\Models\GeoPoint;
use App\Models\GeoPolyline;
use App\Models\GeoPolygon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user
        $user = User::create([
            'name' => 'Demo Officer',
            'email' => 'demo@maiguard.id',
            'password' => Hash::make('demo123'),
        ]);

        // Lombok island land bounds: lat -8.85 to -8.2, lng 115.95 to 116.75
        $latMin = -8.82; $latMax = -8.22;
        $lngMin = 116.00; $lngMax = 116.70;

        // Key cities for realistic clustering
        $cityCenters = [
            ['lat' => -8.585, 'lng' => 116.105, 'name' => 'Mataram'],
            ['lat' => -8.505, 'lng' => 116.045, 'name' => 'Senggigi'],
            ['lat' => -8.718, 'lng' => 116.273, 'name' => 'Praya'],
            ['lat' => -8.355, 'lng' => 116.043, 'name' => 'Tanjung'],
            ['lat' => -8.625, 'lng' => 116.145, 'name' => 'Cakranegara'],
            ['lat' => -8.895, 'lng' => 116.280, 'name' => 'Kuta Selatan'],
            ['lat' => -8.255, 'lng' => 116.425, 'name' => 'Bayan'],
            ['lat' => -8.480, 'lng' => 116.550, 'name' => 'Sembalun'],
            ['lat' => -8.680, 'lng' => 116.200, 'name' => 'Kopang'],
            ['lat' => -8.520, 'lng' => 116.630, 'name' => 'Aikmel'],
            ['lat' => -8.360, 'lng' => 116.160, 'name' => 'Gangga'],
            ['lat' => -8.750, 'lng' => 116.050, 'name' => 'Lembar'],
        ];

        $crimeTypes = ['Theft', 'Assault', 'Vandalism', 'Burglary', 'Robbery', 'Suspicious Activity', 'Drug-related', 'Fraud', 'Harassment', 'Other'];

        // Generate 60 crime spots clustered around cities
        for ($i = 0; $i < 60; $i++) {
            $center = $cityCenters[array_rand($cityCenters)];
            GeoPoint::create([
                'name' => $center['name'] . ' Incident #' . ($i + 1),
                'description' => 'Reported ' . strtolower($crimeTypes[array_rand($crimeTypes)]) . ' near ' . $center['name'] . '.',
                'latitude' => round($center['lat'] + (mt_rand(-300, 300) / 10000), 5),
                'longitude' => round($center['lng'] + (mt_rand(-300, 300) / 10000), 5),
                'crime_type' => $crimeTypes[array_rand($crimeTypes)],
                'user_id' => $user->id,
            ]);
        }

        // Generate 12 patrol routes following actual roads via OSRM
        $routePairs = [
            ['c1' => 'Mataram', 'c2' => 'Senggigi', 'name' => 'Mataram-Senggigi Patrol'],
            ['c1' => 'Praya', 'c2' => 'Kuta Selatan', 'name' => 'Praya-Kuta Route'],
            ['c1' => 'Tanjung', 'c2' => 'Bayan', 'name' => 'Tanjung-Bayan Sweep'],
            ['c1' => 'Senggigi', 'c2' => 'Tanjung', 'name' => 'Coastal Security Route'],
            ['c1' => 'Aikmel', 'c2' => 'Sembalun', 'name' => 'Mountain Patrol Sembalun'],
            ['c1' => 'Mataram', 'c2' => 'Cakranegara', 'name' => 'Urban Mataram Sweep'],
            ['c1' => 'Cakranegara', 'c2' => 'Kopang', 'name' => 'Cakranegara-Kopang Route'],
            ['c1' => 'Lembar', 'c2' => 'Mataram', 'name' => 'Lembar Port Patrol'],
            ['c1' => 'Kopang', 'c2' => 'Aikmel', 'name' => 'Kopang-Aikmel Route'],
            ['c1' => 'Gangga', 'c2' => 'Tanjung', 'name' => 'Gangga Coastal Watch'],
            ['c1' => 'Praya', 'c2' => 'Kopang', 'name' => 'Southern Highway Patrol'],
            ['c1' => 'Mataram', 'c2' => 'Praya', 'name' => 'Airport Security Route'],
        ];

        $cityLookup = [];
        foreach ($cityCenters as $c) { $cityLookup[$c['name']] = $c; }

        foreach ($routePairs as $rp) {
            $c1 = $cityLookup[$rp['c1']];
            $c2 = $cityLookup[$rp['c2']];
            $points = $this->fetchRoute($c1['lng'], $c1['lat'], $c2['lng'], $c2['lat']);

            GeoPolyline::create([
                'name' => $rp['name'],
                'description' => 'Patrol route following actual roads from ' . $c1['name'] . ' to ' . $c2['name'] . '.',
                'coordinates' => $points,
                'crime_type' => $crimeTypes[array_rand($crimeTypes)],
                'user_id' => $user->id,
            ]);
        }

        // Generate 8 crime zones around cities
        $zoneNames = [
            'High-Risk Zone Mataram', 'Red Zone Senggigi', 'Hotspot Cakranegara',
            'Surveillance Zone Praya', 'Danger Zone Kuta', 'Watch Zone Tanjung',
            'Alert Zone Lembar', 'Restricted Zone Kopang',
        ];

        $zoneCities = ['Mataram', 'Senggigi', 'Cakranegara', 'Praya', 'Kuta Selatan', 'Tanjung', 'Lembar', 'Kopang'];

        for ($i = 0; $i < 8; $i++) {
            $center = $cityCenters[array_search($zoneCities[$i], array_column($cityCenters, 'name'))];
            $coords = [];
            $numSides = mt_rand(4, 7);

            for ($j = 0; $j < $numSides; $j++) {
                $angle = ($j / $numSides) * 2 * M_PI;
                $radius = mt_rand(50, 200) / 10000;
                $coords[] = [
                    'lat' => round($center['lat'] + sin($angle) * $radius, 5),
                    'lng' => round($center['lng'] + cos($angle) * $radius, 5),
                ];
            }

            GeoPolygon::create([
                'name' => $zoneNames[$i],
                'description' => 'Designated security zone with elevated monitoring.',
                'coordinates' => $coords,
                'crime_type' => $crimeTypes[array_rand($crimeTypes)],
                'user_id' => $user->id,
            ]);
        }
    }

    private function fetchRoute($lng1, $lat1, $lng2, $lat2): array
    {
        $url = "https://router.project-osrm.org/route/v1/driving/{$lng1},{$lat1};{$lng2},{$lat2}?overview=full&geometries=geojson";
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $response = @file_get_contents($url, false, $ctx);

        if ($response) {
            $data = json_decode($response, true);
            if (($data['code'] ?? '') === 'Ok' && !empty($data['routes'])) {
                $coords = $data['routes'][0]['geometry']['coordinates'];
                return array_map(fn($c) => ['lat' => round($c[1], 5), 'lng' => round($c[0], 5)], $coords);
            }
        }

        // Fallback: straight line with waypoints
        $points = [];
        $steps = 8;
        for ($i = 0; $i <= $steps; $i++) {
            $t = $i / $steps;
            $points[] = [
                'lat' => round($lat1 + ($lat2 - $lat1) * $t + (mt_rand(-40, 40) / 10000), 5),
                'lng' => round($lng1 + ($lng2 - $lng1) * $t + (mt_rand(-40, 40) / 10000), 5),
            ];
        }
        return $points;
    }
}
