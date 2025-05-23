<?php

namespace App\Livewire\Settings;

use App\Models\SystemUpdate as ModelsSystemUpdate;
use Livewire\Component;
use App\Services\System\SystemUpdateService;

class SystemUpdate extends Component
{
    public $updateAvailable = null;
    public $isRunning = false;
    public $log = '';
    public $status = null;
    public $updates = [];

    public function mount(SystemUpdateService $service)
    {
        $this->updateAvailable = $service->checkForUpdates();
        $this->updates = ModelsSystemUpdate::orderBy('created_at', 'desc')->limit(5)->get();
    }

    /**
     * Run the system update and update the Livewire component with the result.
     *
     * @param SystemUpdateService $service
     * @return void
     */
    public function runUpdate(SystemUpdateService $service)
    {
        $this->isRunning = true;
        $this->log = "Updating...\n";

        $update = $service->runUpdate();

        $this->log = $update->update_log;
        $this->status = $update->status;
        $this->isRunning = false;

        $this->mount($service);
    }

    public function render()
    {
        return view('livewire.settings.system-update');
    }
}
