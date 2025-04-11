// empleados.js - Versión mejorada y estable

/**
 * Función principal para guardar empleados
 * @param {Event} event - Evento del formulario
 */
 
 
async function guardarEmpleado(event) {
    event.preventDefault();
    
    const form = document.getElementById('formEmpleado');
    if (!form) {
        console.error('Formulario no encontrado');
        return;
    }

    // Validación básica del formulario
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    try {
        // Obtener token CSRF de forma segura
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            throw new Error('Token de seguridad no encontrado');
        }

        const formData = new FormData(form);
        console.log("Datos a enviar:", Array.from(formData.entries()));

        const response = await fetch('/empleados/guardar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        // Manejo mejorado de la respuesta
        const textResponse = await response.text();
        let data = {};
        
        try {
            data = textResponse ? JSON.parse(textResponse) : {};
        } catch (e) {
            console.error('Error parseando respuesta:', textResponse);
            throw new Error('Respuesta inválida del servidor');
        }

        if (!response.ok) {
            throw new Error(data.message || `Error ${response.status}`);
        }

        if (data.success) {
            $('#modalGestionEmpleado').modal('hide');
            setTimeout(() => window.location.reload(), 1000);
            
            // Mostrar notificación de éxito
            alert('Empleado guardado correctamente');
        } else {
            throw new Error(data.error || 'Error desconocido al guardar');
        }
    } catch (error) {
        console.error("Error completo:", error);
        alert(`Error: ${error.message}`);
    }
}

/**
 * Limpiar formulario al cerrar el modal
 */
$('#modalGestionEmpleado').on('hidden.bs.modal', function () {
    const form = document.getElementById('formEmpleado');
    if (form) {
        form.reset();
        form.classList.remove('was-validated');
    }
    document.getElementById('empleadoId').value = '';
});

/**
 * Cargar áreas al abrir el modal
 */
$('#modalGestionEmpleado').on('show.bs.modal', function () {
    fetch('/empleados/areas')
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar áreas');
            return response.json();
        })
        .then(data => {
            const areaSelect = document.getElementById('area');
            if (areaSelect) {
                areaSelect.innerHTML = '<option value="">Seleccionar...</option>';
                data.forEach(area => {
                    areaSelect.innerHTML += `<option value="${area.id}">${area.nombre}</option>`;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar áreas: ' + error.message);
        });
});

/**
 * Manejar cambio de área (nueva área o cargos)
 */
document.getElementById('area')?.addEventListener('change', function() {
    if (this.value === 'nueva_area') {
        const nuevaArea = prompt('Ingrese el nombre de la nueva área');
        if (nuevaArea) {
            crearNuevaArea(nuevaArea);
        } else {
            this.value = '';
        }
    } else if (this.value) {
        cargarCargos(this.value);
    }
});

/**
 * Función para crear nueva área
 */
function crearNuevaArea(nombreArea) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    fetch('/empleados/crear-area', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ nombre: nombreArea })
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Área creada correctamente');
            $('#modalGestionEmpleado').modal('hide').modal('show');
        } else {
            throw new Error(data.error || 'Error al crear área');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}

/**
 * Función para cargar cargos según área
 */
function cargarCargos(areaId) {
    fetch(`/empleados/cargos/${areaId}`)
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar cargos');
            return response.json();
        })
        .then(data => {
            const cargoSelect = document.getElementById('cargo');
            if (cargoSelect) {
                cargoSelect.innerHTML = '<option value="">Seleccionar...</option>';
                data.forEach(cargo => {
                    cargoSelect.innerHTML += `<option value="${cargo.id}">${cargo.nombre}</option>`;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar cargos: ' + error.message);
        });
}



// Cuando hagas clic en el botón "Ver CBU completo"
// ========== MANEJO SEGURO DE CBU ========== //
$(document).on('click', '.btn-view-cbu', async function() {
    const btn = $(this);
    const empleadoId = btn.data('id');
    const cbuContainer = $(`.cbu-value[data-id="${empleadoId}"]`);

    try {
        // Mostrar loader
        btn.html('<i class="fas fa-spinner fa-spin"></i>');
        
        // 1. Obtener CBU enmascarado
        let response = await fetch(`/empleados/${empleadoId}/cbu?action=masked`);
        if (!response.ok) throw new Error('Error al obtener CBU');
        
        let data = await response.json();
        cbuContainer.text(data.cbu);
        
        // 2. Solicitar contraseña
        const { value: password } = await Swal.fire({
            title: 'Verificación de Seguridad',
            html: `
                <div class="mb-3">
                    <p class="small text-danger">Esta acción queda registrada</p>
                    <input type="password" id="swal-password" class="form-control" 
                           placeholder="Ingrese su contraseña" required>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Ver CBU',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            preConfirm: () => {
                const pass = $('#swal-password').val();
                if (!pass) {
                    Swal.showValidationMessage('La contraseña es requerida');
                }
                return { password: pass };
            }
        });

        if (password) {
            // 3. Obtener CBU completo
            response = await fetch(`/empleados/${empleadoId}/cbu?action=full`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: password.password })
            });
            
            data = await response.json();
            
            if (!response.ok) throw new Error(data.error || 'Error al obtener CBU');
            
            // Mostrar CBU completo y cambiar botones
            cbuContainer.text(data.full_cbu || data.cbu);
            btn.hide();
            $(`.btn-copy-cbu[data-id="${empleadoId}"]`).show();
            
            // Ocultar después de 30 segundos
            setTimeout(() => {
                cbuContainer.text(data.masked_cbu || data.cbu.replace(/(\d{4})\d{14}(\d{4})/, '$1••••••••••••$2'));
                btn.show();
                $(`.btn-copy-cbu[data-id="${empleadoId}"]`).hide();
            }, 30000);
        }
    } catch (error) {
        Swal.fire('Error', error.message, 'error');
    } finally {
        btn.html('<i class="fas fa-eye"></i>');
    }
});

// Función para copiar CBU
$(document).on('click', '.btn-copy-cbu', async function() {
    const empleadoId = $(this).data('id');
    const cbuText = $(`.cbu-value[data-id="${empleadoId}"]`).text();
    
    try {
        await navigator.clipboard.writeText(cbuText);
        Swal.fire({
            icon: 'success',
            title: 'CBU copiado',
            timer: 1500,
            showConfirmButton: false
        });
    } catch (error) {
        Swal.fire('Error', 'No se pudo copiar el CBU', 'error');
    }
});