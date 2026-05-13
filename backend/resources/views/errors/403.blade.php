@extends(auth()->check() ? 'layouts.admin' : 'layouts.guest')

@section('title', 'Access Denied')

@section('content')
    <div class="mx-auto max-w-xl">
        <x-ui.empty-state title="Access denied" message="Your account does not have permission to open this administration area.">
            @auth
                <x-ui.button :href="route('admin.dashboard')" variant="secondary">Return to dashboard</x-ui.button>
            @else
                <x-ui.button :href="route('admin.login')">Sign in</x-ui.button>
            @endauth
        </x-ui.empty-state>
    </div>
@endsection
