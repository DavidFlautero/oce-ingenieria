// empleados.js - Versión optimizada para Bootstrap 5

// Verificar carga de Bootstrap
if (typeof bootstrap === 'undefined') {
    console.error('Bootstrap no está cargado correctamente');
}

/**
 * Función principal para guardar empleados
 */
async function guardarEmpleado(event) {
    event.preventDefault();
    
    const form = document.getElementById('formEmpleado');
    if (!form) return console.error('Formulario no encontrado');

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const formData = new FormData(form);

        const response = await fetch('/empleados/guardar', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });

        const data = await response.json();
        
        if (!response.ok) throw new Error(data.message || `Error ${response.status}`);

        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalGestionEmpleado'));
            modal.hide();
            setTimeout(() => window.location.reload(), 1000);
            alert('Empleado guardado correctamente');
        }
    } catch (error) {
        console.error("Error:", error);
        alert(`Error: ${error.message}`);
    }
}

/**
 * Eventos del Modal
 */
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('modalGestionEmpleado');
    if (!modalEl) return;

    // Limpiar formulario al cerrar
    modalEl.addEventListener('hidden.bs.modal', () => {
        const form = document.getElementById('formEmpleado');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
        }
    });

    // Cargar áreas al abrir
    modalEl.addEventListener('show.bs.modal', () => {
        fetch('/empleados/areas')
            .then(response => response.json())
            .then(areas => {
                const select = document.getElementById('area');
                select.innerHTML = areas.map(area => 
                    `<option value="${area.id}">${area.nombre}</option>`
                ).join('');
            });
    });
});

/**
 * Gestión de Áreas y Cargos
 */
document.getElementById('area')?.addEventListener('change', function() {
    if (this.value === 'nueva_area') {
        const nombre = prompt('Nombre de la nueva área:');
        if (nombre) crearNuevaArea(nombre);
    } else {
        cargarCargos(this.value);
    }
});

function crearNuevaArea(nombre) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/empleados/crear-area', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken 
        },
        body: JSON.stringify({ nombre })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || 'Error al crear área');
        
        alert('Área creada!');
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGestionEmpleado'));
        modal.hide();
        
        // Recargar áreas
        fetch('/empleados/areas')
            .then(res => res.json())
            .then(areas => {
                const select = document.getElementById('area');
                select.innerHTML = areas.map(a => 
                    `<option value="${a.id}">${a.nombre}</option>`
                ).join('');
                modal.show();
            });
    })
    .catch(error => alert(`Error: ${error.message}`));
}

function cargarCargos(areaId) {
    fetch(`/empleados/cargos/${areaId}`)
        .then(response => response.json())
        .then(cargos => {
            const select = document.getElementById('cargo');
            select.innerHTML = cargos.map(cargo => 
                `<option value="${cargo.id}">${cargo.nombre}</option>`
            ).join('');
        });
}

/**
 * Manejo CBU (compatible con jQuery existente)
 */
$(document).on('click', '.btn-view-cbu', async function() {
    const btn = $(this);
    try {
        btn.html('<i class="fas fa-spinner fa-spin"></i>');
        
        const empleadoId = btn.data('id');
        const response = await fetch(`/empleados/${empleadoId}/cbu?action=masked`);
        const data = await response.json();
        
        if (!response.ok) throw new Error(data.error);

        const { value: password } = await Swal.fire({
            title: 'Verificación',
            html: `<input type="password" class="form-control" placeholder="Contraseña" required>`,
            showCancelButton: true,
            preConfirm: () => {
                const pass = $('#swal-password').val();
                if (!pass) Swal.showValidationMessage('Requerido');
                return { password: pass };
            }
        });

        if (password) {
            const fullResponse = await fetch(`/empleados/${empleadoId}/cbu?action=full`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(password)
            });
            
            const fullData = await fullResponse.json();
            if (!fullResponse.ok) throw new Error(fullData.error);
            
            $(`.cbu-value[data-id="${empleadoId}"]`).text(fullData.cbu);
        }
    } catch (error) {
        Swal.fire('Error', error.message, 'error');
    } finally {
        btn.html('<i class="fas fa-eye"></i>');
    }
});