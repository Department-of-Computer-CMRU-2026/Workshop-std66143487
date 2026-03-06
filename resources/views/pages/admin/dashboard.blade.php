<?php

use App\Models\Activity;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Admin Dashboard')] class extends Component {
    public function with()
    {
        $activities = Activity::withCount('registrations')->get();
        
        return [
            'total_activities' => $activities->count(),
            'total_registrations' => $activities->sum('registrations_count'),
            'total_seats' => $activities->sum('total_seats'),
            'full_activities' => $activities->filter(fn($a) => $a->is_full)->count(),
            'activities' => $activities,
        ];
    }
}; ?>

<div class="p-6">
    <flux:heading size="xl" class="mb-6">{{ __('Admin Dashboard') }}</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <flux:card class="flex flex-col items-center justify-center p-6">
            <flux:heading size="sm" class="text-gray-500 uppercase tracking-wider">{{ __('Total Activities') }}</flux:heading>
            <flux:text size="xl" class="font-bold">{{ $total_activities }}</flux:text>
        </flux:card>

        <flux:card class="flex flex-col items-center justify-center p-6">
            <flux:heading size="sm" class="text-gray-500 uppercase tracking-wider">{{ __('Total Registrations') }}</flux:heading>
            <flux:text size="xl" class="font-bold">{{ $total_registrations }} / {{ $total_seats }}</flux:text>
        </flux:card>

        <flux:card class="flex flex-col items-center justify-center p-6">
            <flux:heading size="sm" class="text-gray-500 uppercase tracking-wider">{{ __('Full Activities') }}</flux:heading>
            <flux:text size="xl" class="font-bold text-red-500">{{ $full_activities }}</flux:text>
        </flux:card>

        <flux:card class="flex flex-col items-center justify-center p-6">
            <flux:heading size="sm" class="text-gray-500 uppercase tracking-wider">{{ __('Registration Progress') }}</flux:heading>
            <flux:text size="xl" class="font-bold text-green-500">{{ $total_registrations }} / {{ $total_seats }}</flux:text>
        </flux:card>
    </div>

    <flux:heading size="lg" class="mb-4">{{ __('Activity Oversight') }}</flux:heading>
    
    <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase bg-zinc-50 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Activity') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Registrations') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Total Seats') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Progress') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($activities as $activity)
                    <tr class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $activity->title }}</td>
                        <td class="px-6 py-4">
                            @if ($activity->is_full)
                                <flux:badge variant="danger" size="sm">{{ __('Full') }}</flux:badge>
                            @else
                                <flux:badge variant="success" size="sm">{{ __('Available') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $activity->registrations_count }}</td>
                        <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $activity->total_seats }}</td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $activity->total_seats > 0 ? ($activity->registrations_count / $activity->total_seats * 100) : 0 }}%"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
