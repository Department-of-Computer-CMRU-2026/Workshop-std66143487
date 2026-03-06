<?php

use App\Models\Activity;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Workshops')] class extends Component {
    public function with()
    {
        $user = Auth::user();
        $activities = Activity::withCount('registrations')->get();
        $userRegistrations = $user->registrations()->pluck('activity_id')->toArray();
        $totalUserRegistrations = count($userRegistrations);

        return [
            'activities' => $activities,
            'userRegistrations' => $userRegistrations,
            'canRegisterMore' => $totalUserRegistrations < 3,
            'totalUserRegistrations' => $totalUserRegistrations,
        ];
    }

    public function register(Activity $activity)
    {
        $user = Auth::user();

        // Validate: 1 person <= 3 topics
        if ($user->registrations()->count() >= 3) {
            $this->dispatch('registration-error', message: 'You can only register for a maximum of 3 workshops.');
            return;
        }

        // Validate: Already registered
        if ($user->registrations()->where('activity_id', $activity->id)->exists()) {
            $this->dispatch('registration-error', message: 'You are already registered for this workshop.');
            return;
        }

        // Validate: Seats available
        if ($activity->remaining_seats <= 0) {
            $this->dispatch('registration-error', message: 'Sorry, this workshop is full.');
            return;
        }

        Registration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
        ]);

        $this->dispatch('registration-success', message: 'Successfully registered for ' . $activity->title);
    }

    public function cancelRegistration(Activity $activity)
    {
        $user = Auth::user();
        $user->registrations()->where('activity_id', $activity->id)->delete();
        $this->dispatch('registration-success', message: 'Successfully cancelled registration for ' . $activity->title);
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">{{ __('Available Workshops') }}</flux:heading>
            <flux:subheading>{{ __('Discover and register for upcoming activities.') }}</flux:subheading>
        </div>
        <flux:badge variant="{{ $totalUserRegistrations >= 3 ? 'danger' : 'neutral' }}" size="lg">
            {{ __('My Registrations: :count/3', ['count' => $totalUserRegistrations]) }}
        </flux:badge>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($activities as $activity)
            @php
                $isRegistered = in_array($activity->id, $userRegistrations);
            @endphp
            <flux:card class="flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex justify-between items-start">
                        <flux:heading size="lg">{{ $activity->title }}</flux:heading>
                        @if ($isRegistered)
                            <flux:badge variant="success" size="sm" icon="check">{{ __('Registered') }}</flux:badge>
                        @elseif ($activity->remaining_seats <= 0)
                            <flux:badge variant="danger" size="sm">{{ __('Closed') }}</flux:badge>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-gray-500">
                            <flux:icon.user size="sm" />
                            <flux:text size="sm">{{ $activity->speaker }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500">
                            <flux:icon.map-pin size="sm" />
                            <flux:text size="sm">{{ $activity->location }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500">
                            <flux:icon.users size="sm" />
                            <flux:text size="sm">
                                REGISTERED: {{ $activity->registrations_count }} / {{ $activity->total_seats }}
                            </flux:text>
                        </div>
                    </div>

                    <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2 mt-4">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $activity->total_seats > 0 ? ($activity->registrations_count / $activity->total_seats * 100) : 0 }}%"></div>
                    </div>
                </div>

                <div class="mt-6">
                    @if ($isRegistered)
                        <flux:button wire:click="cancelRegistration({{ $activity->id }})" variant="danger" class="w-full">
                            {{ __('Cancel Registration') }}
                        </flux:button>
                    @else
                        <flux:button 
                            wire:click="register({{ $activity->id }})" 
                            variant="primary" 
                            class="w-full"
                            :disabled="!$canRegisterMore || $activity->remaining_seats <= 0"
                        >
                            @if ($activity->remaining_seats <= 0)
                                {{ __('Closed') }}
                            @elseif (!$canRegisterMore)
                                {{ __('Limit Reached (3/3)') }}
                            @else
                                {{ __('Register Now') }}
                            @endif
                        </flux:button>
                    @endif
                </div>
            </flux:card>
        @endforeach
    </div>

    <x-action-message class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg" on="registration-success">
        <span x-text="$event.detail.message"></span>
    </x-action-message>

    <x-action-message class="fixed bottom-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg" on="registration-error">
        <span x-text="$event.detail.message"></span>
    </x-action-message>
</div>
