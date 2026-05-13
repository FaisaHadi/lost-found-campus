@extends('layouts.admin')

@section('title', 'Report Detail')

@section('page-actions')
    <x-ui.button :href="route('admin.reports.index')" variant="secondary">Back to reports</x-ui.button>
@endsection

@section('content')
    @php
        $imageUrl = $report->image_path ? Illuminate\Support\Facades\Storage::disk('public')->url($report->image_path) : null;
    @endphp

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-ui.card title="Report overview" subtitle="Submission details and moderation context">
                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Title</p>
                        <p class="mt-1 text-base font-semibold text-gray-950">{{ $report->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Reporter</p>
                        <p class="mt-1 text-base font-semibold text-gray-950">{{ $report->user?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $report->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <x-ui.badge :value="$report->status?->value ?? $report->status" />
                            <x-ui.badge :value="$report->moderation_status?->value ?? $report->moderation_status" />
                            <x-ui.badge :value="$report->report_type?->value ?? $report->report_type" />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Location</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $report->location_text ?: 'No location text' }}</p>
                        <p class="text-sm text-gray-500">{{ $report->latitude ?? 'No latitude' }}, {{ $report->longitude ?? 'No longitude' }}</p>
                    </div>
                    <div class="lg:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $report->description }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Claims on this report" subtitle="Submitted ownership proof associated with this report">
                @forelse ($report->claims as $claim)
                    <div class="border-b border-gray-100 py-4 first:pt-0 last:border-b-0 last:pb-0">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <a href="{{ route('admin.claims.show', $claim) }}" class="text-sm font-semibold text-gray-950 hover:text-emerald-700">
                                    {{ $claim->claimant?->name }}
                                </a>
                                <p class="mt-1 text-sm text-gray-600">{{ str($claim->proof_text)->limit(180) }}</p>
                            </div>
                            <x-ui.badge :value="$claim->status?->value ?? $claim->status" />
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state title="No claims submitted" message="Claims from users will appear here after this report is approved." />
                @endforelse
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Moderation actions" subtitle="Approve, reject, or correct report status">
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.reports.approve', $report) }}" data-loading-form>
                        @csrf
                        @method('PATCH')
                        <x-ui.button data-loading-button data-loading-text="Approving...">Approve</x-ui.button>
                    </form>
                    <form method="POST" action="{{ route('admin.reports.reject', $report) }}" data-loading-form>
                        @csrf
                        @method('PATCH')
                        <x-ui.button variant="danger" data-loading-button data-loading-text="Rejecting...">Reject</x-ui.button>
                    </form>
                </div>
            </x-ui.card>

            <x-ui.card title="Image" subtitle="Current report evidence">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}" alt="Report image" class="aspect-video w-full rounded-lg border border-gray-200 object-cover">
                @else
                    <x-ui.empty-state title="No image" message="This report does not have uploaded evidence." />
                @endif
            </x-ui.card>
        </div>
    </div>

    <x-ui.card title="Edit report" subtitle="Update report metadata, status, and image evidence" class="mt-6">
        <form method="POST" action="{{ route('admin.reports.update', $report) }}" enctype="multipart/form-data" class="grid gap-5 lg:grid-cols-2" data-loading-form>
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="admin-label">Title</label>
                <input id="title" name="title" value="{{ old('title', $report->title) }}" class="admin-control mt-1" required>
            </div>

            <div>
                <label for="category_id" class="admin-label">Category</label>
                <select id="category_id" name="category_id" class="admin-control mt-1">
                    <option value="">Uncategorized</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $report->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="report_type" class="admin-label">Type</label>
                <select id="report_type" name="report_type" class="admin-control mt-1">
                    <option value="lost" @selected(old('report_type', $report->report_type?->value ?? $report->report_type) === 'lost')>Lost</option>
                    <option value="found" @selected(old('report_type', $report->report_type?->value ?? $report->report_type) === 'found')>Found</option>
                </select>
            </div>

            <div>
                <label for="status" class="admin-label">Status</label>
                <select id="status" name="status" class="admin-control mt-1">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $report->status?->value ?? $report->status) === $status)>{{ str($status)->title() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="latitude" class="admin-label">Latitude</label>
                <input id="latitude" name="latitude" value="{{ old('latitude', $report->latitude) }}" class="admin-control mt-1">
            </div>

            <div>
                <label for="longitude" class="admin-label">Longitude</label>
                <input id="longitude" name="longitude" value="{{ old('longitude', $report->longitude) }}" class="admin-control mt-1">
            </div>

            <div class="lg:col-span-2">
                <label for="location_text" class="admin-label">Location text</label>
                <input id="location_text" name="location_text" value="{{ old('location_text', $report->location_text) }}" class="admin-control mt-1">
            </div>

            <div class="lg:col-span-2">
                <label for="description" class="admin-label">Description</label>
                <textarea id="description" name="description" rows="5" class="admin-control mt-1" required>{{ old('description', $report->description) }}</textarea>
            </div>

            <div class="lg:col-span-2">
                <label class="admin-label">Replace image</label>
                <div data-dropzone data-max-size="4194304" class="mt-1 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-5 transition">
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    <label for="image" class="block cursor-pointer text-center">
                        <img data-dropzone-preview hidden alt="Selected preview" class="mx-auto mb-4 aspect-video max-h-56 rounded-lg border border-gray-200 object-cover">
                        <span class="text-sm font-medium text-gray-950">Drop an image here or browse from your computer</span>
                        <span class="mt-1 block text-xs text-gray-500">JPG, PNG, or WEBP up to 4 MB</span>
                        <span data-dropzone-filename class="mt-2 block text-sm text-emerald-700"></span>
                    </label>
                    <p data-dropzone-error class="mt-3 text-center text-sm text-rose-600"></p>
                </div>
                @if ($report->image_path)
                    <label class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remove_image" value="1" class="admin-focus rounded border-gray-300 text-emerald-600">
                        Remove current image without uploading a replacement
                    </label>
                @endif
            </div>

            <div class="lg:col-span-2">
                <label for="reason" class="admin-label">Reason for rejection or audit note</label>
                <textarea id="reason" name="reason" rows="3" class="admin-control mt-1">{{ old('reason') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 lg:col-span-2">
                <x-ui.button :href="route('admin.reports.index')" variant="secondary">Cancel</x-ui.button>
                <x-ui.button data-loading-button data-loading-text="Saving...">Save changes</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
