<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Google\Cloud\Core\GeoPoint;

class SeedLagosZone extends Command
{
    protected $signature = 'zone:seed-lagos';
    protected $description = 'Create Lagos zone in Firestore if none exists';

    public function handle()
    {
        $credentialsPath = public_path(env('FIREBASE_CREDENTIALS', 'firebase.json'));

        if (!file_exists($credentialsPath)) {
            $this->error("Firebase credentials not found at: {$credentialsPath}");
            return 1;
        }

        $factory = (new Factory)->withServiceAccount($credentialsPath);
        $firestore = $factory->createFirestore()->database();

        $zoneCollection = $firestore->collection('zone');

        // Check if any zones already exist
        $existing = $zoneCollection->limit(1)->documents();
        foreach ($existing as $doc) {
            if ($doc->exists()) {
                $this->info('Zone already exists (id: ' . $doc->id() . '). Nothing to do.');
                return 0;
            }
        }

        // Generate a new document ID
        $docRef = $zoneCollection->newDocument();
        $id = $docRef->id();

        // Simplified polygon covering Lagos State, Nigeria
        $area = [
            new GeoPoint(6.3939, 3.1715),
            new GeoPoint(6.6962, 3.1954),
            new GeoPoint(6.6962, 3.5876),
            new GeoPoint(6.4698, 3.7165),
            new GeoPoint(6.3939, 3.4800),
        ];

        $docRef->set([
            'id'        => $id,
            'name'      => [
                ['name' => 'Lagos', 'type' => 'en'],
            ],
            'latitude'  => 6.5244,
            'longitude' => 3.3792,
            'area'      => $area,
            'publish'   => true,
        ]);

        $this->info("Lagos zone created successfully (id: {$id})");
        return 0;
    }
}
