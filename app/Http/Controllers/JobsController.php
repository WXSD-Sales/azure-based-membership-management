<?php

namespace App\Http\Controllers;

use App\Jobs\PerformCrossSync;
use App\Jobs\RefreshAzureToken;
use App\Jobs\RefreshWebexToken;
use App\Jobs\RetrieveAzureGroups;
use App\Jobs\RetrieveAzureUsers;
use App\Jobs\RetrieveWebexGroups;
use App\Jobs\RetrieveWebexUsers;
use Illuminate\Support\Facades\Log;

class JobsController extends Controller
{

//    public function refreshAzureToken()
//    {
//        Log::info('refreshAzureToken');
//        RefreshAzureToken::dispatch();
//    }

//    public function refreshWebexToken()
//    {
//        Log::info('refreshWebexToken');
//        RefreshWebexToken::dispatch();
//    }
    public function retrieveAzureGroups()
    {
        Log::info('retrieveAzureGroups');
        RetrieveAzureGroups::dispatch();
    }
    public function retrieveAzureUsers()
    {
        Log::info('retrieveAzureUsers');
        RetrieveAzureUsers::dispatchSync();
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

    public function performCrossSync()
    {
        Log::info('performCrossSync');
        PerformCrossSync::dispatch();
    }

}
