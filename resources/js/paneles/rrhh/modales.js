// resources/js/paneles/rrhh/modales.js
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';
import { popularFormulario } from './empleados';
import { obtenerEmpleado, cargarAreas, cargarCargos } from './api';
import { actualizarProgresoPerfil } from './validaciones';

// Modal de nuevo empleado
export function abrirModalNuevo() {
    const modalElement = document.getElementById('modalGestionEmpleado');
    if (!modalElement) {
        console.error('Modal no encontrado');
        return;
    }

    // Configuración inicial del modal
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Nuevo Empleado';
    document.getElementById('btnEliminar').style.display = 'none';
    document.getElementById('formEmpleado').reset();
    document.getElementById('empleadoId').value = '';
    
    // Reset estado del perfil
    resetearEstadoPerfil();

    // Cargar áreas al abrir modal
    cargarAreas().then(areas => {
        const select = document.getElementById('area');
        if (select) {
            select.innerHTML = areas.map(area => 
                `<option value="${area.id}">${area.nombre}</option>`
            ).join('') + '<option value="nueva_area">+ Crear Nueva Área</option>';
        }
    }).catch(error => {
        console.error('Error al cargar áreas:', error);
    });

    // Mostrar modal
    const modal = new Modal(modalElement);
    modal.show();
}

// Modal de edición de empleado
export async function abrirModalEditar(id) {
    try {
        const empleado = await obtenerEmpleado(id);
        
        // Popular formulario con datos del empleado
        popularFormulario(empleado);
        
        // Configurar modal
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>Editar Empleado';
        document.getElementById('btnEliminar').style.display = 'block';
        document.getElementById('empleadoId').value = id;
        
        // Cargar áreas y cargos
        await cargarAreas().then(areas => {
            const select = document.getElementById('area');
            if (select) {
                select.innerHTML = areas.map(area => 
                    `<option value="${area.id}" ${area.id == empleado.area ? 'selected' : ''}>${area.nombre}</option>`
                ).join('') + '<option value="nueva_area">+ Crear Nueva Área</option>';
            }
        });
        
        if (empleado.area) {
            await cargarCargos(empleado.area).then(cargos => {
                const select = document.getElementById('cargo');
                if (select) {
                    select.innerHTML = cargos.map(cargo => 
                        `<option value="${cargo.id}" ${cargo.id == empleado.cargo ? 'selected' : ''}>${cargo.nombre}</option>`
                    );
                }
            });
        }
        
        // Estado del perfil completo
        const estadoPerfil = document.getElementById('estadoPerfilModal');
        const progresoPerfil = document.getElementById('progresoPerfil');
        
        if (estadoPerfil && progresoPerfil) {
            estadoPerfil.className = 'badge bg-success ms-2';
            estadoPerfil.textContent = 'Completo';
            progresoPerfil.style.width = '100%';
            progresoPerfil.className = 'progress-bar bg-success';
        }

        // Mostrar modal
        const modal = new Modal(document.getElementById('modalGestionEmpleado'));
        modal.show();
    } catch (error) {
        console.error('Error al abrir modal de edición:', error);
        Swal.fire('Error', 'No se pudo cargar el empleado', 'error');
    }
}

// Modal de baja de empleado
export function iniciarBaja(id) {
    const modalElement = document.getElementById('modalBaja');
    if (!modalElement) {
        console.error('Modal de baja no encontrado');
        return;
    }

    document.getElementById('bajaEmpleadoId').value = id;
    
    // Resetear formulario de baja
    const formBaja = document.getElementById('formBaja');
    if (formBaja) {
        formBaja.reset();
    }
    
    const modal = new Modal(modalElement);
    modal.show();
}

// Modal de confirmación genérico
export function mostrarModalConfirmacion(config) {
    return Swal.fire({
        title: config.title || '¿Estás seguro?',
        text: config.text || 'Esta acción no se puede deshacer',
        icon: config.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: config.confirmText || 'Sí, continuar',
        cancelButtonText: config.cancelText || 'Cancelar',
        ...config
    });
}

// Mostrar campos extras según condición impositiva
export function mostrarCamposExtras() {
    const condicion = document.getElementById('condicionImpositiva')?.value;
    const alerta = document.getElementById('alertaDependencia');
    const camposExtras = document.getElementById('camposExtras');
    const categoriaMono = document.getElementById('categoriaMonotributoContainer');

    // Ocultar todos primero
    if (alerta) alerta.style.display = 'none';
    if (camposExtras) camposExtras.style.display = 'none';
    if (categoriaMono) categoriaMono.style.display = 'none';

    // Mostrar según condición
    if (condicion === 'relacion_dependencia' && alerta) {
        alerta.style.display = 'block';
    }

    if (condicion === 'monotributista' || condicion === 'autonomo') {
        if (camposExtras) camposExtras.style.display = 'flex';
        if (condicion === 'monotributista' && categoriaMono) {
            categoriaMono.style.display = 'block';
        }
    }
}

// Resetear estado del perfil
function resetearEstadoPerfil() {
    const estadoPerfil = document.getElementById('estadoPerfilModal');
    const progresoPerfil = document.getElementById('progresoPerfil');
    
    if (estadoPerfil && progresoPerfil) {
        estadoPerfil.className = 'badge bg-warning ms-2';
        estadoPerfil.textContent = 'Incompleto';
        progresoPerfil.style.width = '0%';
        progresoPerfil.className = 'progress-bar bg-warning';
    }
}

// Inicializar eventos de modales
export function initModalEvents() {
    // Evento para cuando se muestra el modal
    document.getElementById('modalGestionEmpleado')?.addEventListener('show.bs.modal', function() {
        actualizarProgresoPerfil();
    });

    // Evento para cuando se oculta el modal
    document.getElementById('modalGestionEmpleado')?.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('formEmpleado');
        if (form) {
            form.classList.remove('was-validated');
            
            // Limpiar errores de validación
            const invalidElements = form.querySelectorAll('.is-invalid');
            invalidElements.forEach(el => el.classList.remove('is-invalid'));
        }
    });

    // Evento para cambio de condición impositiva
    document.getElementById('condicionImpositiva')?.addEventListener('change', mostrarCamposExtras);
}