<section>

<div class="mt-10 sm:mt-0">
    <form method="POST" action="{{ route('profile.language.update') }}">
        @csrf
        @method('PUT')

        <div class="shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 bg-white sm:p-6">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-4">
                        <x-input-label for="language" :value="__('User Language Preference')" />
                        <div class="mt-2 space-y-2">
                            @foreach([
                                'es' => __('Spanish'),
                                'ca' => __('Catalan'),
                                'en' => __('English'),
                            ] as $code => $label)
                                <label class="flex items-center">
                                    <input type="radio" 
                                        name="language" 
                                        value="{{ $code }}"
                                        {{ $code == auth()->user()->getSetting('language', config('app.locale')) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('language')" class="mt-2" />
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                <x-primary-button>
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</div>