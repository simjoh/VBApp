<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PingController extends Controller
{
    /**
     * Retrieve system status
     *
     * @return Response
     */
    public function ping()
    {

        // Test database connection
        try {
           $conn = DB::connection()->getPdo();
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }
        return response()->json(['status' => 'Helathy']);
    }
}
