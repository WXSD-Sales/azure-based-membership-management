<?php

namespace App\Http\Controllers;

use App\Models\Cisco\WebexGroup;
use App\Models\Microsoft\AzureGroup;
use App\Models\Microsoft\AzureUser;
use App\Models\SyncMapping;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Log;

class DashboardController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('dashboard');
    }

    public function getSyncMappings(){
        return SyncMapping::with(['azureGroup.users', 'webexGroup.users'])
            ->get()
            ->toArray();
    }

    public function getAzureSyncMappings(){
        return AzureGroup::with(['syncMapping'])
            ->get()
            ->toArray();
    }

    public function getWebexSyncMappings(){
        return WebexGroup::with(['syncMapping'])
            ->get()
            ->toArray();
    }

    public function getWebexGroups(){
        return WebexGroup::all()
            ->toArray();
    }

    public function getAzureGroups(){
        return AzureGroup::all()
            ->toArray();
    }

    public function getAzureUsers(){
        return AzureUser::all()
            ->toArray();
    }

    public function getWebexUsers(){
        return AzureUser::all()
            ->toArray();
    }
}
