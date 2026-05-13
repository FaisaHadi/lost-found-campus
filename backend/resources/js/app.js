import './bootstrap';

const toggleAttribute = (target, attribute = 'data-open') => {
    if (!target) {
        return;
    }

    target.toggleAttribute(attribute);
};

document.querySelectorAll('[data-loading-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('[data-loading-button]');

        if (!button) {
            return;
        }

        button.disabled = true;
        button.dataset.originalText = button.textContent.trim();
        button.textContent = button.dataset.loadingText || 'Memproses...';
    });
});

document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const sidebar = document.querySelector('[data-admin-sidebar]');
        const overlay = document.querySelector('[data-sidebar-overlay]');

        toggleAttribute(sidebar);
        toggleAttribute(overlay);
    });
});

document.querySelectorAll('[data-notification-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        toggleAttribute(document.querySelector('[data-notification-menu]'));
    });
});

document.addEventListener('click', (event) => {
    const menu = document.querySelector('[data-notification-menu]');
    const toggle = document.querySelector('[data-notification-toggle]');

    if (!menu || !menu.hasAttribute('data-open')) {
        return;
    }

    if (menu.contains(event.target) || toggle?.contains(event.target)) {
        return;
    }

    menu.removeAttribute('data-open');
});

document.querySelectorAll('[data-dropzone]').forEach((dropzone) => {
    const input = dropzone.querySelector('input[type="file"]');
    const preview = dropzone.querySelector('[data-dropzone-preview]');
    const filename = dropzone.querySelector('[data-dropzone-filename]');
    const error = dropzone.querySelector('[data-dropzone-error]');
    const maxSize = Number(dropzone.dataset.maxSize || 4194304);
    const acceptedTypes = (dropzone.dataset.accept || 'image/jpeg,image/png,image/webp').split(',');

    const clearError = () => {
        if (error) {
            error.textContent = '';
        }
    };

    const showError = (message) => {
        if (error) {
            error.textContent = message;
        }
    };

    const handleFile = (file) => {
        clearError();

        if (!file) {
            return;
        }

        if (!acceptedTypes.includes(file.type)) {
            input.value = '';
            showError('Gunakan gambar JPG, PNG, atau WEBP.');
            return;
        }

        if (file.size > maxSize) {
            input.value = '';
            showError('Ukuran gambar maksimal 4 MB.');
            return;
        }

        if (filename) {
            filename.textContent = file.name;
        }

        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.removeAttribute('hidden');
        }
    };

    dropzone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropzone.classList.add('dropzone-active');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dropzone-active');
    });

    dropzone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropzone.classList.remove('dropzone-active');

        const file = event.dataTransfer.files[0];

        if (!file || !input) {
            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        handleFile(file);
    });

    input?.addEventListener('change', () => handleFile(input.files[0]));
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    });
}
