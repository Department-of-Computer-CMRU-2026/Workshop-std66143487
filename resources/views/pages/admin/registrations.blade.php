<?php

use App\Models\Activity;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Registrations')] class extends Component {
    public $selectedActivityId = null;

    public function mount()
    {
        // Default to first activity if available
        $this->selectedActivityId = Activity::first()?->id;
    }

    public function with()
    {
        return [
            'activities' => Activity::all(),
            'registrations' => $this->selectedActivityId 
                ? Activity::find($this->selectedActivityId)?->registrations()->with('user')->get() 
                : collect(),
        ];
    }
}; ?>

<div class="p-6">
    <flux:heading size="xl" class="mb-6">{{ __('User Registrations') }}</flux:heading>

    <div class="mb-6 w-72">
        <flux:select wire:model.live="selectedActivityId" :label="__('Select Activity')">
            <option value="">{{ __('-- Select Activity --') }}</option>
            @foreach ($activities as $activity)
                <option value="{{ $activity->id }}">{{ $activity->title }}</option>
            @endforeach
        </flux:select>
    </div>

    @if ($selectedActivityId)
        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-zinc-50 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Email') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Registered At') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($registrations as $reg)
                        <tr class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $reg->user->name }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $reg->user->email }}</td>
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $reg->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr class="bg-white dark:bg-zinc-900">
                            <td colspan="3" class="px-6 py-8 text-center text-zinc-500">{{ __('No registrations yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="p-12 text-center border-2 border-dashed rounded-xl border-neutral-200 dark:border-neutral-700">
            <flux:subheading>{{ __('Please select an activity to view registrations.') }}</flux:subheading>
        </div>
    @endif
</div>
