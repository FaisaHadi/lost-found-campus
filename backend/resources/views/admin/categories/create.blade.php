@extends('layouts.admin')

@section('title', 'Create Category')

@section('page-actions')
    <x-ui.button :href="route('admin.categories.index')" variant="secondary">Back to categories</x-ui.button>
@endsection

@section('content')
    <div class="max-w-2xl">
        <x-ui.card title="Category details" subtitle="Categories help administrators and users filter reports consistently">
            @include('admin.categories._form', [
                'action' => route('admin.categories.store'),
                'method' => 'POST',
                'submitLabel' => 'Create category',
            ])
        </x-ui.card>
    </div>
@endsection
