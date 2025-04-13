// resources/js/app.js

import './bootstrap';
import '../css/app.scss';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import EmpleadosRRHH from './paneles/rrhh/empleados';

// Configura Alpine
window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

// Inicia Livewire
Livewire.start();

// Inicializa RRHH cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new EmpleadosRRHH();
    
    // Tooltips globales
    $('[data-bs-toggle="tooltip"]').tooltip();
});