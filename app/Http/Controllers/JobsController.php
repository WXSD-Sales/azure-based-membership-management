<?php

namespace App\Http\Controllers;

use App\Jobs\PerformCrossSync;
use App\Jobs\RefreshAzureToken;
use App\Jobs\RefreshWebexToken;
use App\Jobs\RetrieveAzureGroups;
use App\Jobs\RetrieveAzureUsers;
use App\Jobs\RetrieveWebexGroups;
use App\Jobs\RetrieveWebexUsers;
use App\Models\SyncMapping;
use Exception;
use Illuminate\Support\Facades\Log;

class JobsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function refreshAzureToken()
    {
        Log::info('[JobsController] refreshAzureToken');
        try{
            RefreshAzureToken::dispatchSync();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function refreshWebexToken()
    {
        Log::info('[JobsController] refreshWebexToken');
        try{
            RefreshWebexToken::dispatchSync();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function retrieveAzureUsers()
    {
        Log::info('[JobsController] retrieveAzureUsers');
        try{
            RetrieveAzureUsers::dispatchSync();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function retrieveAzureGroups()
    {
        Log::info('[JobsController] retrieveAzureGroups');
        try{
            RetrieveAzureGroups::dispatchSync();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function retrieveWebexUsers()
    {
        Log::info('[JobsController] retrieveWebexUsers');
        try{
            RetrieveWebexUsers::dispatchSync();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function retrieveWebexGroups()
    {
        Log::info('[JobsController] retrieveWebexGroups');
        try{
            RetrieveWebexGroups::dispatchSync();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function performCrossSync()
    {
        Log::info('[JobsController] performCrossSync');
        try {
            PerformCrossSync::dispatchSync();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }
}
