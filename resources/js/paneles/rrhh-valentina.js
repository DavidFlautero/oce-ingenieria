// Inicializar plugins de AdminLTE
document.addEventListener('DOMContentLoaded', function() {
    // Activar tooltips (opcional)
    $('[data-toggle="tooltip"]').tooltip();
    
    // Inicializar dropdowns
    $('.dropdown-toggle').dropdown();
    
 


    // Actualizar fecha
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('es-AR', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });

    // Toggle filtros
    function toggleFiltros() {
        const filtros = document.getElementById('filtrosAvanzados');
        filtros.style.display = filtros.style.display === 'none' ? 'block' : 'none';
    }

    // Funciones para el modal
    window.abrirModalNuevo = function () {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Nuevo Empleado';
    document.getElementById('btnEliminar').style.display = 'none';
    document.getElementById('formEmpleado').reset();
    document.getElementById('empleadoId').value = '';
    document.getElementById('estadoPerfilModal').className = 'badge bg-warning ms-2';
    document.getElementById('estadoPerfilModal').textContent = 'Incompleto';
    document.getElementById('progresoPerfil').style.width = '0%';
    document.getElementById('progresoPerfil').className = 'progress-bar bg-warning';

    const modal = new bootstrap.Modal(document.getElementById('modalGestionEmpleado'));
    modal.show();
}


    function abrirModalEditar(id) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>Editar Empleado';
    document.getElementById('btnEliminar').style.display = 'block';
    document.getElementById('empleadoId').value = id;

    // Limpiar el formulario por las dudas
    document.getElementById('formEmpleado').reset();
	
	function mostrarCamposExtras() {
    const condicion = document.getElementById('condicionImpositiva').value;
    const alerta = document.getElementById('alertaDependencia');
    const camposExtras = document.getElementById('camposExtras');
    const categoriaMono = document.getElementById('categoriaMonotributoContainer');

    alerta.style.display = 'none';
    camposExtras.style.display = 'none';
    categoriaMono.style.display = 'none';

    if (condicion === 'relacion_dependencia') {
        alerta.style.display = 'block';
    }

    if (condicion === 'monotributista' || condicion === 'autonomo') {
        camposExtras.style.display = 'flex';

        if (condicion === 'monotributista') {
            categoriaMono.style.display = 'block';
        }
    }
}


    fetch(`/empleados/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener los datos del empleado');
            }
            return response.json();
        })
        .then(empleado => {
            document.getElementById('nombre').value = empleado.nombre;
            document.getElementById('apellido').value = empleado.apellido;
            document.getElementById('dni').value = empleado.dni;
            document.getElementById('fechaNacimiento').value = empleado.fecha_nacimiento;
            document.getElementById('grupoSanguineo').value = empleado.grupo_sanguineo;
            document.getElementById('telefono').value = empleado.telefono;
            document.getElementById('direccion').value = empleado.direccion;
            document.getElementById('alergias').value = empleado.alergias;
            document.getElementById('cbu').value = empleado.cbu;
            document.getElementById('fechaIngreso').value = empleado.fecha_ingreso;
            document.getElementById('area').value = empleado.area;
            document.getElementById('cargo').value = empleado.cargo;
            document.getElementById('tipoContrato').value = empleado.tipo_contrato;
            document.getElementById('salarioBase').value = empleado.salario_base;
            document.getElementById('bonificaciones').value = empleado.bonificaciones;
            document.getElementById('contactoEmergenciaNombre').value = empleado.contacto_emergencia_nombre;
            document.getElementById('contactoEmergenciaTelefono').value = empleado.contacto_emergencia_telefono;
            document.getElementById('contactoEmergenciaParentesco').value = empleado.contacto_emergencia_parentesco;

            // Estado del perfil (esto podes personalizarlo mejor si tenes lógica específica)
            document.getElementById('estadoPerfilModal').className = 'badge bg-success ms-2';
            document.getElementById('estadoPerfilModal').textContent = 'Completo';
            document.getElementById('progresoPerfil').style.width = '100%';
            document.getElementById('progresoPerfil').className = 'progress-bar bg-success';

            const modal = new bootstrap.Modal(document.getElementById('modalGestionEmpleado'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al cargar los datos del empleado.');
        });
}


    function iniciarBaja(id) {
        document.getElementById('bajaEmpleadoId').value = id;
        const modal = new bootstrap.Modal(document.getElementById('modalBaja'));
        modal.show();
    }

   function guardarEmpleado(e) {
    e.preventDefault();
    
    // Validar campos requeridos
    const camposRequeridos = document.querySelectorAll('[required]');
    let faltantes = 0;
    
    camposRequeridos.forEach(campo => {
        if(!campo.value) {
            campo.classList.add('is-invalid');
            faltantes++;
        } else {
            campo.classList.remove('is-invalid');
        }
    });
    
    if(faltantes > 0) {
        alert(`Por favor complete los ${faltantes} campos obligatorios faltantes`);
        return;
    }
    
    // Preparar datos
    const formData = new FormData();
    formData.append('empleadoId', document.getElementById('empleadoId').value);
    formData.append('nombreCompleto', document.getElementById('nombreCompleto').value);
    formData.append('dni', document.getElementById('dni').value);
    // Append los otros campos...

    fetch('/empleados/guardar', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Empleado guardado correctamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalGestionEmpleado'));
            modal.hide();
            location.reload();
        } else {
            alert('Error al guardar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error inesperado al guardar');
    });
}


    // Validar CBU
    document.getElementById('cbu').addEventListener('input', function() {
        if(this.value.length !== 22 || !/^\d+$/.test(this.value)) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    // Validar progreso del perfil
    document.getElementById('formEmpleado').addEventListener('input', function() {
        const camposRequeridos = document.querySelectorAll('[required]');
        let completados = 0;
        
        camposRequeridos.forEach(campo => {
            if(campo.value) completados++;
        });
        
        const porcentaje = Math.round((completados / camposRequeridos.length) * 100);
        document.getElementById('progresoPerfil').style.width = porcentaje + '%';
        
        if(porcentaje === 100) {
            document.getElementById('estadoPerfilModal').className = 'badge bg-success ms-2';
            document.getElementById('estadoPerfilModal').textContent = 'Completo';
            document.getElementById('progresoPerfil').className = 'progress-bar bg-success';
        } else {
            document.getElementById('estadoPerfilModal').className = 'badge bg-warning ms-2';
            document.getElementById('estadoPerfilModal').textContent = `Incompleto (${camposRequeridos.length - completados} faltantes)`;
            document.getElementById('progresoPerfil').className = 'progress-bar bg-warning';
        }
    });
