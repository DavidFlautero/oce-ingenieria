// empleados.js

// Guardar Empleado
// En tu empleados.js - CORREGIDO Y PROBADO:

async function guardarEmpleado(event) {
    event.preventDefault();
    
    // Debug: Ver datos antes de enviar
    const formData = new FormData(document.getElementById('formEmpleado'));
    console.log("Datos a enviar:", Array.from(formData.entries()));

    try {
        const response = await fetch('/empleados/guardar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();
        console.log("Respuesta del servidor:", data);

        if (!response.ok) {
            throw new Error(data.message || 'Error en el servidor');
        }

        if (data.success) {
            $('#modalGestionEmpleado').modal('hide');
            setTimeout(() => window.location.reload(), 1000); // Espera 1s antes de recargar
        } else {
            throw new Error(data.error || 'Error al guardar');
        }
    } catch (error) {
        console.error("Error completo:", error);
        alert(`Error: ${error.message}`);
    }
}

// Limpiar formulario cuando se cierra el modal de Gestión de Empleado
$('#modalGestionEmpleado').on('hidden.bs.modal', function () {
    document.getElementById('formEmpleado').reset(); // Limpia los campos del form
    document.getElementById('empleadoId').value = ''; // Limpia el ID oculto
});

// Cargar las Áreas desde la base de datos al abrir el modal
$('#modalGestionEmpleado').on('show.bs.modal', function () {
    fetch('/empleados/areas')
        .then(response => response.json())
        .then(data => {
            let areaSelect = document.getElementById('area');
            areaSelect.innerHTML = '<option value="">Seleccionar...</option>'; // Limpiar
            data.forEach(area => {
                areaSelect.innerHTML += `<option value="${area.nombre}">${area.nombre}</option>`;
            });
        });
});

document.getElementById('area').addEventListener('change', function () {
    if(this.value === 'nueva_area'){
        let nuevaArea = prompt('Ingrese el nombre de la nueva área');

        if(nuevaArea){
            // Guardar en la base de datos
            fetch('/empleados/crear-area', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ nombre: nuevaArea })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    alert('Área creada correctamente');
                    $('#modalGestionEmpleado').trigger('show.bs.modal'); // Vuelve a cargar las áreas
                }
            });
        } else {
            this.value = ''; // Si canceló, vuelve a seleccionar vacío
        }
    } else {
        // Si no es nueva área, cargar los cargos
        fetch('/empleados/cargos/' + this.value)
            .then(response => response.json())
            .then(data => {
                let cargoSelect = document.getElementById('cargo');
                cargoSelect.innerHTML = '<option value="">Seleccionar...</option>';
                data.forEach(cargo => {
                    cargoSelect.innerHTML += `<option value="${cargo.nombre}">${cargo.nombre}</option>`;
                });
            });
    }
});

// Cargar los Cargos según el Área seleccionada
document.getElementById('area').addEventListener('change', function () {
    let areaSeleccionada = this.value;
    fetch('/empleados/cargos/' + areaSeleccionada)
        .then(response => response.json())
        .then(data => {
            let cargoSelect = document.getElementById('cargo');
            cargoSelect.innerHTML = '<option value="">Seleccionar...</option>'; // Limpiar
            data.forEach(cargo => {
                cargoSelect.innerHTML += `<option value="${cargo.nombre}">${cargo.nombre}</option>`;
            });
        });
});

