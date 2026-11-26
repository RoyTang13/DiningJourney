import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const menuToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');
const searchType = document.querySelector('input[name="type"]');

menuToggle?.addEventListener('click', () => {
    const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!expanded));
    navLinks?.classList.toggle('open', !expanded);
});

const profileMenu = document.querySelector('[data-profile-menu]');
if (profileMenu instanceof HTMLElement) {
    const profileMenuTrigger = profileMenu.querySelector('.profile-menu-trigger');
    const profileMenuDropdown = profileMenu.querySelector('.profile-menu-dropdown');

    if (profileMenuTrigger instanceof HTMLButtonElement && profileMenuDropdown instanceof HTMLElement) {
        const closeProfileMenu = () => {
            profileMenuTrigger.setAttribute('aria-expanded', 'false');
            profileMenuDropdown.hidden = true;
        };

        profileMenuTrigger.addEventListener('click', () => {
            const expanded = profileMenuTrigger.getAttribute('aria-expanded') === 'true';
            profileMenuTrigger.setAttribute('aria-expanded', String(!expanded));
            profileMenuDropdown.hidden = expanded;
        });

        document.addEventListener('click', (event) => {
            if (event.target instanceof Node && !profileMenu.contains(event.target)) {
                closeProfileMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && profileMenuTrigger.getAttribute('aria-expanded') === 'true') {
                closeProfileMenu();
                profileMenuTrigger.focus();
            }
        });
    }
}

document.querySelectorAll('.search-tab').forEach((tab) => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.search-tab').forEach((item) => item.classList.remove('active'));
        tab.classList.add('active');
        if (searchType) {
            searchType.value = tab.dataset.category;
        }
    });
});

const photoInput = document.querySelector('#profile-photo-input');
const photoModal = document.querySelector('#photo-editor-modal');
const cropImage = document.querySelector('#photo-crop-image');
const photoFeedback = document.querySelector('[data-photo-feedback]');
const editorFeedback = document.querySelector('[data-editor-feedback]');
const photoPreview = document.querySelector('#photo-circle-preview');
const savePhotoButton = document.querySelector('[data-save-photo]');

if (photoInput instanceof HTMLInputElement
    && photoModal instanceof HTMLDialogElement
    && cropImage instanceof HTMLImageElement
    && photoFeedback instanceof HTMLElement
    && editorFeedback instanceof HTMLElement
    && photoPreview instanceof HTMLElement
    && savePhotoButton instanceof HTMLButtonElement) {
    const uploadForm = document.querySelector('#profile-photo-upload');
    const avatar = document.querySelector('[data-profile-photo]');
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxFileSize = 10 * 1024 * 1024;
    let cropper = null;
    let selectedPhotoUrl = null;

    const setFeedback = (element, message, isError = false) => {
        element.textContent = message;
        element.classList.toggle('is-error', isError);
    };

    const cleanUpEditor = () => {
        cropper?.destroy();
        cropper = null;
        photoPreview.replaceChildren();
        cropImage.removeAttribute('src');
        if (selectedPhotoUrl) {
            URL.revokeObjectURL(selectedPhotoUrl);
            selectedPhotoUrl = null;
        }
    };

    const openEditor = () => photoInput.click();

    document.querySelectorAll('[data-open-photo-editor]').forEach((button) => {
        button.addEventListener('click', openEditor);
    });

    photoInput.addEventListener('change', () => {
        const file = photoInput.files?.[0];
        photoInput.value = '';
        setFeedback(photoFeedback, '');

        if (!file) {
            return;
        }
        if (!allowedTypes.includes(file.type)) {
            setFeedback(photoFeedback, 'Choose a JPG, PNG, or WEBP image.', true);
            return;
        }
        if (file.size > maxFileSize) {
            setFeedback(photoFeedback, 'Choose an image that is 10 MB or smaller.', true);
            return;
        }

        cleanUpEditor();
        setFeedback(editorFeedback, '');
        selectedPhotoUrl = URL.createObjectURL(file);
        photoModal.showModal();
        cropImage.onload = () => {
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                autoCropArea: 0.9,
                background: false,
                dragMode: 'move',
                guides: true,
                movable: true,
                preview: '#photo-circle-preview',
                responsive: true,
                viewMode: 1,
                zoomOnWheel: true,
            });
        };
        cropImage.onerror = () => {
            setFeedback(editorFeedback, 'This image could not be opened. Please choose another one.', true);
        };
        cropImage.src = selectedPhotoUrl;
    });

    document.querySelectorAll('[data-zoom-photo]').forEach((button) => {
        button.addEventListener('click', () => {
            if (cropper && button instanceof HTMLButtonElement) {
                cropper.zoom(Number(button.dataset.zoomPhoto) * 0.1);
            }
        });
    });

    document.querySelector('[data-reset-photo]')?.addEventListener('click', () => cropper?.reset());

    document.querySelectorAll('[data-cancel-photo-editor]').forEach((button) => {
        button.addEventListener('click', () => photoModal.close());
    });

    photoModal.addEventListener('close', cleanUpEditor);

    savePhotoButton.addEventListener('click', () => {
        if (!cropper || !(uploadForm instanceof HTMLFormElement) || !(avatar instanceof HTMLImageElement)) {
            setFeedback(editorFeedback, 'The image editor is not ready yet. Please wait or choose the image again.', true);
            return;
        }

        const canvas = cropper.getCroppedCanvas({
            width: 512,
            height: 512,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        if (!canvas) {
            setFeedback(editorFeedback, 'The selected image could not be cropped. Please try again.', true);
            return;
        }

        savePhotoButton.disabled = true;
        setFeedback(editorFeedback, 'Saving your photo…');
        canvas.toBlob(async (blob) => {
            if (!blob) {
                savePhotoButton.disabled = false;
                setFeedback(editorFeedback, 'The cropped image could not be prepared. Please try again.', true);
                return;
            }

            const formData = new FormData();
            formData.append('photo', blob, 'profile-photo.jpg');
            try {
                const response = await fetch(uploadForm.dataset.uploadUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': uploadForm.querySelector('input[name="_token"]').value,
                    },
                    body: formData,
                });
                let result;
                try {
                    result = await response.json();
                } catch {
                    savePhotoButton.disabled = false;
                    setFeedback(editorFeedback, 'The server returned an unreadable response. Please try again.', true);
                    return;
                }

                if (!response.ok) {
                    const message = result.errors?.photo?.[0] || result.message || 'The photo could not be saved.';
                    savePhotoButton.disabled = false;
                    setFeedback(editorFeedback, message, true);
                    return;
                }
                if (typeof result.url !== 'string') {
                    savePhotoButton.disabled = false;
                    setFeedback(editorFeedback, 'The server did not return the saved photo. Please try again.', true);
                    return;
                }

                avatar.src = `${result.url}${result.url.includes('?') ? '&' : '?'}v=${Date.now()}`;
                setFeedback(photoFeedback, result.message || 'Profile photo updated.');
                photoModal.close();
            } catch {
                savePhotoButton.disabled = false;
                setFeedback(editorFeedback, 'Could not reach the server. Check your connection and try again.', true);
            }
        }, 'image/jpeg', 0.9);
    });
}
