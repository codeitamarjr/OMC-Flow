<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\System\SystemUpdateService;

class SystemUpdate extends Component
{
    public $updateAvailable = null;
    public $isRunning = false;
    public $log = '';
    public $status = null;

    public function mount(SystemUpdateService $service)
    {
        $this->updateAvailable = $service->checkForUpdates();
    }

    public function runUpdate(SystemUpdateService $service)
    {
        $this->isRunning = true;
        $this->log = "Updating...\n";

        $update = $service->runUpdate();

        $this->log = $update->update_log;
        $this->status = $update->status;
        $this->isRunning = false;
    }

    public function render()
    {
        return view('livewire.settings.system-update');
    }
}
