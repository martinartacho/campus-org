<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <i class="bi bi-pencil-square mr-2"></i> 
            {{ __('site.Edit_notification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('notifications.update', $notification) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 mt-4">
                            <!-- Titol -->
                            <div class="col-span-1">
                                <x-input-label for="title" value="{{__('site.Title')}}" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="title" 
                                    value="{{ old('title', $notification->title) }}" required />
                            </div>

                        <!-- Contenido -->
                        <div>
                           <x-input-label for="content" value="{{__('site.Content')}}" />
                            <textarea id="content" name="content" rows="5"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      required>{{ old('content', $notification->content) }}</textarea>
                        </div>

                        <!-- Tipo de destinatario -->
                        <div>
                           <x-input-label for="recipient_type" value="{{__('site.Recipients')}}" />
                            <select id="recipient_type" name="recipient_type" x-model="recipientType"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                @foreach($recipientTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                          <!-- Tipo de notificación -->
                        <div>
                            <x-input-label for="type" value="{{ __('site.Notification_Type')}}" />
                            <select id="type" name="type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="new" {{ old('type') == 'new' ? 'selected' : '' }}>{{ __('site.Type_New') }}</option>
                                <option value="feedback" {{ old('type') == 'feedback' ? 'selected' : '' }}>{{ __('site.Type_Feedback') }}</option>
                                <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>{{ __('site.Type_System') }}</option>
                            </select>
                        </div>    

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button class="ml-4">
                                    {{ __('site.Update Notification') }}
                                </x-primary-button>
                            </div>
                            <div class="flex justify-between pt-4">
                                <a href="{{ route('notifications.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    Volver {{ __('site.go_back') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
