@extends('layouts.admin')

@section('title', 'Edit Category')

@section('page-actions')
    <x-ui.button :href="route('admin.categories.index')" variant="secondary">Back to categories</x-ui.button>
@endsection

@section('content')
    <div class="max-w-2xl">
        <x-ui.card title="Category details" subtitle="Keep naming, status, and descriptions clear for all platform clients">
            @include('admin.categories._form', [
                'action' => route('admin.categories.update', $category),
                'method' => 'PUT',
                'submitLabel' => 'Save category',
            ])
        </x-ui.card>
    </div>
@endsection
