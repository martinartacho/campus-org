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