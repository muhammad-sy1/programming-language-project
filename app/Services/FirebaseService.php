// app/Services/FirebaseService.php
<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    public static function firestore()
    {
        return (new Factory)
            ->withServiceAccount(storage_path('firebase/serviceAccount.json'))
            ->createFirestore()
            ->database();
    }
}
