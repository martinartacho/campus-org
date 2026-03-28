<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <i class="bi bi-pencil-square mr-2"></i> 
            {{ __('site.Edit_notification') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        recipientType: '{{ old('recipient_type', $notification->recipient_type) }}'
    }">
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
                            <textarea id="content-editor" name="content" rows="5"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      required>{{ old('content', $notification->content) }}</textarea>
                        </div>

                        <!-- Tipo de destinatario -->
                        <div>
                           <x-input-label for="recipient_type" value="{{__('site.Recipients')}}" />
                            <select id="recipient_type" name="recipient_type" x-model="recipientType" @change="recipientType = $event.target.value"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                @foreach($recipientTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('recipient_type', $notification->recipient_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campo para roles -->
                        <div x-show="recipientType === 'role'" x-transition>
                           <x-input-label for="recipient_role" value="{{__('site.Select_Role')}}" />
                            <select id="recipient_role" name="recipient_role"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($roles as $id => $name)
                                    <option value="{{ $name }}" {{ old('recipient_role', $notification->recipient_role) == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campo para usuarios específicos -->
                        <div x-show="recipientType === 'specific'" x-transition>
                           <x-input-label for="recipient_ids" value="{{__('site.Select_Users')}}" /> 
                            <select id="recipient_ids" name="recipient_ids[]" multiple
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('recipient_ids', $notification->recipient_ids ?? [])) ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">{{ __('site.info_select_multiple_users') }}.</p>
                        </div>

                          <!-- Tipo de notificación -->
                        <div>
                            <x-input-label for="type" value="{{ __('site.Notification_Type')}}" />
                            <select id="type" name="type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="new" {{ old('type', $notification->type) == 'new' ? 'selected' : '' }}>{{ __('site.Type_New') }}</option>
                                <option value="feedback" {{ old('type', $notification->type) == 'feedback' ? 'selected' : '' }}>{{ __('site.Type_Feedback') }}</option>
                                <option value="system" {{ old('type', $notification->type) == 'system' ? 'selected' : '' }}>{{ __('site.Type_System') }}</option>
                            </select>
                        </div>

                        <!-- Estado de publicación -->
                        <div>
                            <x-input-label for="is_published" value="{{ __('site.Publish_Status') }}" />
                            <select id="is_published" name="is_published"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="0" {{ old('is_published', $notification->is_published) == 0 ? 'selected' : '' }}>{{ __('site.Draft') }}</option>
                                <option value="1" {{ old('is_published', $notification->is_published) == 1 ? 'selected' : '' }}>{{ __('site.Published') }}</option>
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

@push('styles')
<style>
.content {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    max-width: 600px;
    margin: 0 auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.content h3 {
    color: #333;
    margin-bottom: 15px;
    margin-top: 25px;
}
.content p {
    margin-bottom: 15px;
}
.content ul {
    margin-bottom: 15px;
    padding-left: 20px;
}
.content li {
    margin-bottom: 5px;
}
.content strong {
    color: #007bff;
}
</style>
@endpush

@stack('scripts')
<script>
$(document).ready(function() {
    $('#content-editor').summernote({
        lang: 'ca-ES',
        height: 300,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video', 'table', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onInit: function() {
                $('.note-editable').css('min-height', '200px');
            }
        }
    });
});
</script>
@endstack
