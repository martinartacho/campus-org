@extends('campus.shared.layout')

@section('title', __('Configuració PDF - Treasury'))

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="bg-white border-b border-gray-200 px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="bi bi-file-earmark-pdf text-blue-600 mr-2"></i>
                {{ __('Configuració PDF i Pagaments') }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ __('Gestiona les dates límit per PDFs i períodes de congelació de pagaments') }}
            </p>
        </div>

        <div class="p-6 space-y-8">
            <!-- Secció Data Límit de PDF -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="bi bi-calendar-date text-blue-600 mr-2"></i>
                    {{ __('Data Límit de PDF') }}
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Configurar la data límit per a l'actualització de PDFs de consentiment dels professors.
                </p>
                
                <form method="POST" action="{{ route('treasury.settings.updatePdfDeadline') }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="pdf_update_deadline" :value="__('Data Límit')" />
                            <x-text-input 
                                id="pdf_update_deadline"
                                name="pdf_update_deadline"
                                type="date"
                                :value="\App\Models\Setting::get('pdf_update_deadline', '2026-03-15')"
                                class="mt-1 block w-full"
                                required
                            />
                            <x-input-error :messages="$errors->get('pdf_update_deadline')" class="mt-2" />
                        </div>
                        <div class="flex items-end">
                            <x-primary-button type="submit">
                                <i class="bi bi-save mr-2"></i>
                                {{ __('Actualitzar Data Límit') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Secció Període de Congelació de Pagaments -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="bi bi-snow text-blue-600 mr-2"></i>
                    {{ __('Període de Congelació de Pagaments') }}
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Configurar el període durant el qual les dades bancàries dels professors estaran congelades per evitar errors durant els pagaments.
                </p>
                
                <form method="POST" action="{{ route('treasury.settings.updatePaymentFreeze') }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="payment_freeze_start" :value="__('Data Inici Congelació')" />
                            <x-text-input 
                                id="payment_freeze_start"
                                name="payment_freeze_start"
                                type="date"
                                :value="\App\Models\Setting::get('payment_freeze_start', '')"
                                class="mt-1 block w-full"
                            />
                            <x-input-error :messages="$errors->get('payment_freeze_start')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="payment_freeze_end" :value="__('Data Fi Congelació')" />
                            <x-text-input 
                                id="payment_freeze_end"
                                name="payment_freeze_end"
                                type="date"
                                :value="\App\Models\Setting::get('payment_freeze_end', '')"
                                class="mt-1 block w-full"
                            />
                            <x-input-error :messages="$errors->get('payment_freeze_end')" class="mt-2" />
                        </div>
                        <div class="flex items-end">
                            <x-primary-button type="submit">
                                <i class="bi bi-save mr-2"></i>
                                {{ __('Actualitzar Període') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Botó de tornada -->
            <div class="flex justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Tornar al Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
