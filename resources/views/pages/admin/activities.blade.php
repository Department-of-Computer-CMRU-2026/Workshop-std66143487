<?php

use App\Models\Activity;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Manage Activities')] class extends Component {
    public $activities;
    public $editing = null;
    public $showModal = false;

    // Form fields
    public $title = '';
    public $speaker = '';
    public $location = '';
    public $total_seats = 0;

    public function mount()
    {
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $this->activities = Activity::withCount('registrations')->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->editing = null;
        $this->showModal = true;
    }

    public function edit(Activity $activity)
    {
        $this->editing = $activity;
        $this->title = $activity->title;
        $this->speaker = $activity->speaker;
        $this->location = $activity->location;
        $this->total_seats = $activity->total_seats;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'total_seats' => 'required|integer|min:1',
        ]);

        if ($this->editing) {
            $this->editing->update([
                'title' => $this->title,
                'speaker' => $this->speaker,
                'location' => $this->location,
                'total_seats' => $this->total_seats,
            ]);
        } else {
            Activity::create([
                'title' => $this->title,
                'speaker' => $this->speaker,
                'location' => $this->location,
                'total_seats' => $this->total_seats,
            ]);
        }

        $this->showModal = false;
        $this->loadActivities();
    }

    public function delete(Activity $activity)
    {
        $activity->delete();
        $this->loadActivities();
    }

    private function resetForm()
    {
        $this->title = '';
        $this->speaker = '';
        $this->location = '';
        $this->total_seats = 0;
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ __('Activities') }}</flux:heading>
        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Add Activity') }}</flux:button>
    </div>

    <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase bg-zinc-50 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Title') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Speaker') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Location') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Seats') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($activities as $activity)
                    <tr class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $activity->title }}</td>
                        <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $activity->speaker }}</td>
                        <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $activity->location }}</td>
                        <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $activity->registrations_count }} / {{ $activity->total_seats }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <flux:button wire:click="edit({{ $activity->id }})" size="sm" icon="pencil-square"></flux:button>
                                <flux:button wire:click="delete({{ $activity->id }})" size="sm" variant="danger" icon="trash" wire:confirm="{{ __('Are you sure?') }}"></flux:button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <flux:modal wire:model="showModal" name="activity-modal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editing ? __('Edit Activity') : __('Add Activity') }}</flux:heading>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:input wire:model="title" :label="__('Title')" required />
                <flux:input wire:model="speaker" :label="__('Speaker')" required />
                <flux:input wire:model="location" :label="__('Location')" required />
                <flux:input type="number" wire:model="total_seats" :label="__('Total Seats')" required />

                <div class="flex justify-end gap-2 mt-6">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
