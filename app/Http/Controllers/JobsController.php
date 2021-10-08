<?php

namespace App\Http\Controllers;

use App\Jobs\RetrieveAzureGroups;
use App\Jobs\RetrieveAzureUsers;
use App\Jobs\RetrieveWebexGroups;
use App\Jobs\RetrieveWebexUsers;
use Illuminate\Support\Facades\Log;

class JobsController extends Controller
{

    public function retrieveAzureGroups()
    {
        Log::info('retrieveAzureGroups');
        RetrieveAzureGroups::dispatch();
    }

    public function retrieveAzureUsers()
    {
        Log::info('retrieveAzureUsers');
        RetrieveAzureUsers::dispatch();
    }

    public function retrieveWebexUsers()
    {
        Log::info('retrieveWebexUsers');
        RetrieveWebexUsers::dispatch();
    }

    public function retrieveWebexGroups()
    {
        Log::info('retrieveWebexGroups');
        RetrieveWebexGroups::dispatch();
    }
}
