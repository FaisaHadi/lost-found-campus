<form method="POST" action="{{ $action }}" class="space-y-5" data-loading-form>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label for="name" class="admin-label">Nama</label>
        <input id="name" name="name" value="{{ old('name', $category->name) }}" class="admin-control mt-1" required>
    </div>

    <div>
        <label for="slug" class="admin-label">Slug</label>
        <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" class="admin-control mt-1" placeholder="Dibuat dari nama jika dikosongkan">
    </div>

    <div>
        <label for="description" class="admin-label">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="admin-control mt-1">{{ old('description', $category->description) }}</textarea>
    </div>

    <div>
        <label for="status" class="admin-label">Status</label>
        <select id="status" name="status" class="admin-control mt-1" required>
            <option value="active" @selected(old('status', $category->status ?: 'active') === 'active')>Aktif</option>
            <option value="inactive" @selected(old('status', $category->status) === 'inactive')>Tidak aktif</option>
        </select>
    </div>

    <div class="flex justify-end gap-2">
        <x-ui.button :href="route('admin.categories.index')" variant="secondary">Batal</x-ui.button>
        <x-ui.button data-loading-button data-loading-text="Menyimpan...">{{ $submitLabel }}</x-ui.button>
    </div>
</form>
