import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import Swal from 'sweetalert2';

window.Swal = Swal;

window.showLoader = function(form) {
    Swal.fire({
        title: 'Enviando notificaciones',
        html: 'Por favor espera...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            
            // Envía el formulario después de mostrar el loader
            setTimeout(() => {
                form.submit();
            }, 100);
        }
    });
};

// Importar y montar el componente de ayuda clásico
import ClassicHelpButton from './components/Help/ClassicHelpButton.vue';

// Crear aplicación Vue para el botón de ayuda
import { createApp } from 'vue';

const helpApp = createApp({
    components: {
        ClassicHelpButton
    }
});

// Montar el botón de ayuda
helpApp.mount('#help-button-container');