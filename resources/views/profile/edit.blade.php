<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[var(--color-text)] dark:text-[var(--color-text)] leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Avatar --}}
            <div class="p-4 sm:p-8 bg-[var(--color-surface)] dark:bg-[var(--color-surface)] border border-[var(--color-border)] dark:border-[var(--color-border)] shadow-sm sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Profile Picture') }}</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Click the avatar to upload, then crop & compress.') }}</p>
                        </header>

                        <form method="post" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" id="avatarForm">
                            @csrf
                            <div class="mt-6 flex flex-col items-center gap-4">
                                {{-- Avatar preview --}}
                                <label for="avatarInput" id="avatarLabel" style="cursor:pointer; position:relative; display:block;">
                                    <div id="avatarPreview" style="width:96px; height:96px; border-radius:50%; overflow:hidden; border:2px dashed var(--color-border); display:flex; align-items:center; justify-content:center; transition:all 0.2s ease; background:var(--color-bg);">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ url('/files/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <span style="font-size:2rem; font-weight:700; color:var(--color-accent-text); text-transform:uppercase;">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div id="avatarOverlay" style="position:absolute; inset:0; border-radius:50%; background:rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s ease;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                    </div>
                                </label>
                                <input type="file" id="avatarInput" accept="image/*" style="display:none;">

                                <div style="display:flex; gap:0.5rem;">
                                    <button type="button" onclick="document.getElementById('avatarInput').click()" class="inline-flex items-center px-4 py-2 bg-[var(--color-accent)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--color-accent-hover)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:ring-offset-2 dark:focus:ring-offset-[var(--color-surface)] transition ease-in-out duration-150">
                                        {{ __('Upload') }}
                                    </button>
                                    <button type="submit" id="submitBtn" style="display:none;" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition ease-in-out duration-150">
                                        {{ __('Save Crop') }}
                                    </button>
                                    @if(Auth::user()->avatar)
                                    <button type="button" onclick="removeAvatar()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        {{ __('Remove') }}
                                    </button>
                                    @endif
                                </div>

                                @if (session('status') === 'avatar-updated')
                                    <p class="text-sm text-green-600 dark:text-green-400">{{ __('Avatar updated.') }}</p>
                                @endif
                                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                            </div>
                        </form>

                        {{-- Crop Modal --}}
                        <div id="cropModal" style="display:none; position:fixed; inset:0; z-index:3000; background:rgba(0,0,0,0.75); align-items:center; justify-content:center;">
                            <div style="background:var(--color-surface); border-radius:var(--radius-lg); padding:1.5rem; max-width:90vw; max-height:90vh; display:flex; flex-direction:column; align-items:center; gap:1rem;">
                                <h3 style="color:var(--color-text); font-weight:600; font-size:1rem;">Crop your picture</h3>
                                <div style="max-width:400px; max-height:400px;">
                                    <img id="cropImage" style="max-width:100%; display:block;">
                                </div>
                                <div style="display:flex; gap:0.75rem;">
                                    <button type="button" onclick="cancelCrop()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">Cancel</button>
                                    <button type="button" onclick="applyCrop()" class="inline-flex items-center px-4 py-2 bg-[var(--color-accent)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[var(--color-accent-hover)] transition ease-in-out duration-150">Crop & Save</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
            <script>
                let cropper = null;

                const avatarLabel = document.getElementById('avatarLabel');
                const avatarOverlay = document.getElementById('avatarOverlay');

                avatarLabel.addEventListener('mouseenter', () => avatarOverlay.style.opacity = '1');
                avatarLabel.addEventListener('mouseleave', () => avatarOverlay.style.opacity = '0');

                // Drag & drop
                avatarLabel.addEventListener('dragover', e => { e.preventDefault(); avatarOverlay.style.opacity = '1'; avatarLabel.querySelector('#avatarPreview').style.borderColor = 'var(--color-accent)'; });
                avatarLabel.addEventListener('dragleave', e => { e.preventDefault(); avatarOverlay.style.opacity = '0'; avatarLabel.querySelector('#avatarPreview').style.borderColor = 'var(--color-border)'; });
                avatarLabel.addEventListener('drop', e => {
                    e.preventDefault();
                    avatarOverlay.style.opacity = '0';
                    avatarLabel.querySelector('#avatarPreview').style.borderColor = 'var(--color-border)';
                    if (e.dataTransfer.files.length) {
                        document.getElementById('avatarInput').files = e.dataTransfer.files;
                        openCropper(e.dataTransfer.files[0]);
                    }
                });

                document.getElementById('avatarInput').addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        openCropper(this.files[0]);
                    }
                });

                function openCropper(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const cropImage = document.getElementById('cropImage');
                        cropImage.src = e.target.result;
                        document.getElementById('cropModal').style.display = 'flex';
                        if (cropper) cropper.destroy();
                        cropper = new Cropper(cropImage, {
                            aspectRatio: 1,
                            viewMode: 1,
                            autoCropArea: 1,
                            movable: true,
                            zoomable: true,
                            rotatable: false,
                            scalable: false,
                        });
                    };
                    reader.readAsDataURL(file);
                }

                function cancelCrop() {
                    document.getElementById('cropModal').style.display = 'none';
                    if (cropper) { cropper.destroy(); cropper = null; }
                    document.getElementById('avatarInput').value = '';
                }

                function applyCrop() {
                    if (!cropper) return;
                    const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
                    canvas.toBlob(function(blob) {
                        // Update preview
                        const url = URL.createObjectURL(blob);
                        document.getElementById('avatarPreview').innerHTML = `<img src="${url}" style="width:100%;height:100%;object-fit:cover;">`;

                        // Build FormData manually with the cropped blob
                        const formData = new FormData();
                        formData.append('avatar', blob, 'avatar.jpg');
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route('profile.avatar') }}', {
                            method: 'POST',
                            body: formData,
                            headers: { 'Accept': 'application/json' }
                        })
                        .then(res => {
                            if (res.redirected) { window.location.href = res.url; return; }
                            return res.json().then(data => { throw new Error(data.message || 'Upload failed'); });
                        })
                        .catch(err => { alert('Upload failed: ' + err.message); });

                        cancelCrop();
                    }, 'image/jpeg', 0.8);
                }

                function removeAvatar() {
                    if (!confirm('Remove your profile picture?')) return;
                    const form = document.getElementById('avatarForm');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_remove_avatar';
                    input.value = '1';
                    form.appendChild(input);
                    form.submit();
                }
            </script>

            <div class="p-4 sm:p-8 bg-[var(--color-surface)] dark:bg-[var(--color-surface)] border border-[var(--color-border)] dark:border-[var(--color-border)] shadow-sm sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-[var(--color-surface)] dark:bg-[var(--color-surface)] border border-[var(--color-border)] dark:border-[var(--color-border)] shadow-sm sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-[var(--color-surface)] dark:bg-[var(--color-surface)] border border-[var(--color-border)] dark:border-[var(--color-border)] shadow-sm sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
