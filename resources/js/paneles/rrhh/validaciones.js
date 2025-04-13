// resources/js/paneles/rrhh/validaciones.js
import Swal from 'sweetalert2';

export function validarCBU(event) {
    const input = event.target;
    if (input.value.length !== 22 || !/^\d+$/.test(input.value)) {
        input.classList.add('is-invalid');
        return false;
    } else {
        input.classList.remove('is-invalid');
        return true;
    }
}

export function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

export function validarDNI(dni) {
    return /^\d{7,8}$/.test(dni);
}

export function actualizarProgresoPerfil() {
    const camposRequeridos = document.querySelectorAll('[required]');
    let completados = 0;
    
    camposRequeridos.forEach(campo => {
        if (campo.value && campo.value.trim() !== '') {
            // Validación adicional para campos específicos
            if (campo.id === 'dni' && !validarDNI(campo.value)) return;
            if (campo.id === 'email' && !validarEmail(campo.value)) return;
            if (campo.id === 'cbu' && !validarCBU({target: campo})) return;
            
            completados++;
        }
    });
    
    const porcentaje = Math.round((completados / camposRequeridos.length) * 100);
    const progreso = document.getElementById('progresoPerfil');
    const estado = document.getElementById('estadoPerfilModal');
    
    if (!progreso || !estado) return;
    
    progreso.style.width = `${porcentaje}%`;
    
    if (porcentaje === 100) {
        estado.className = 'badge bg-success ms-2';
        estado.textContent = 'Completo';
        progreso.className = 'progress-bar bg-success';
    } else {
        estado.className = 'badge bg-warning ms-2';
        estado.textContent = `Incompleto (${camposRequeridos.length - completados} faltantes)`;
        progreso.className = 'progress-bar bg-warning';
    }
}

export function validarFormulario(event) {
    event.preventDefault();
    const form = event.target;
    let isValid = true;
    
    // Validar campos requeridos
    const camposRequeridos = form.querySelectorAll('[required]');
    camposRequeridos.forEach(campo => {
        if (!campo.value || campo.value.trim() === '') {
            campo.classList.add('is-invalid');
            isValid = false;
        } else {
            campo.classList.remove('is-invalid');
            
            // Validaciones específicas
            if (campo.id === 'dni' && !validarDNI(campo.value)) {
                campo.classList.add('is-invalid');
                isValid = false;
            }
            
            if (campo.id === 'email' && !validarEmail(campo.value)) {
                campo.classList.add('is-invalid');
                isValid = false;
            }
            
            if (campo.id === 'cbu' && !validarCBU({target: campo})) {
                campo.classList.add('is-invalid');
                isValid = false;
            }
        }
    });
    
    if (!isValid) {
        form.classList.add('was-validated');
        Swal.fire({
            icon: 'error',
            title: 'Campos incompletos',
            text: 'Por favor complete todos los campos requeridos correctamente',
            footer: 'Los campos marcados en rojo son obligatorios'
        });
        return false;
    }
    
    return true;
}