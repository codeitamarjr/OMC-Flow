@props(['companies', 'categories', 'providers'])
<div class="mt-4 space-y-4 text-left">
    <flux:select wire:model.defer="company_id" :label="__('Company')">
        <option value="">{{ __('Select Company') }}</option>
        @foreach ($companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
        @endforeach
    </flux:select>

    <flux:select wire:model.live="service_category_id" :label="__('Service Category')">
        <option value="">{{ __('Select Category') }}</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </flux:select>

    <flux:select wire:model.defer="service_provider_id" :label="__('Service Provider')">
        <option value="">{{ __('Select Provider') }}</option>
        @foreach ($providers as $provider)
            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
        @endforeach
    </flux:select>

    <flux:input wire:model.defer="budget" :label="__('Budget (€)')" type="number" step="0.01" />
    <flux:input wire:model.defer="start_date" :label="__('Start Date')" type="date" />
    <flux:input wire:model.defer="next_due_date" :label="__('Next Due Date')" type="date" />

    <flux:select wire:model.defer="status" :label="__('Status')">
        <option value="active">{{ __('Active') }}</option>
        <option value="inactive">{{ __('Inactive') }}</option>
        <option value="terminated">{{ __('Terminated') }}</option>
    </flux:select>

    <flux:input wire:model.defer="notes" :label="__('Notes')" />
</div>
