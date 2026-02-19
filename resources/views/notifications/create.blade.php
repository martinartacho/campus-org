<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('site.Create Notification') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" 
                     x-data="{ recipientType: '{{ old('recipient_type', 'all') }}' }">

                    <form method="POST" action="{{ route('notifications.store') }}" class="space-y-6">
                        @csrf

                        <!-- Título -->
                        <div>
                           <x-input-label for="title" value="{{ __('site.Title')}}" />
                           <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                        </div>

                        <!-- Contenido -->
                        <div>
                           <x-input-label for="content" value="{{__('site.Content')}}" /> 
                            <textarea id="content" name="content" rows="5"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      required>{{ old('content') }}</textarea>
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

                        <!-- Campo para roles -->
                        <div x-show="recipientType === 'role'" x-transition>
                           <x-input-label for="recipient_role" value="{{__('site.Select_Role')}}" />
                            <select id="recipient_role" name="recipient_role"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($roles as $id => $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campo para usuarios específicos -->
                        <div x-show="recipientType === 'specific'" x-transition>
                           <x-input-label for="recipient_ids" value="{{__('site.Select_Users')}}" /> 
                            <select id="recipient_ids" name="recipient_ids[]" multiple
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">{{ __('site.info_select_multiple_users') }}.</p>
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
                        <!-- Botón -->
                        <div class="pt-4">
                            <x-primary-button class="ml-4">
                                {{ __('site.Save_Notification') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
