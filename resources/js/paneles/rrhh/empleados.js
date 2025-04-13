// resources/js/paneles/rrhh/empleados.js
import { abrirModalNuevo, abrirModalEditar, iniciarBaja, mostrarCamposExtras, initModalEvents } from './modales';
import { validarCBU, actualizarProgresoPerfil, validarFormulario } from './validaciones';
import { guardarEmpleadoAPI, crearNuevaArea, cargarCargos } from './api';

export default class EmpleadosRRHH {
    constructor() {
        this.initEventListeners();
        initModalEvents();
        this.setCurrentDate();
    }

    initEventListeners() {
        // Toggle Filtros
        document.getElementById('toggleFiltros')?.addEventListener('click', this.toggleFiltros);
        
        // Evento para CBU
        document.getElementById('cbu')?.addEventListener('input', validarCBU);
        
        // Progreso del perfil
        document.getElementById('formEmpleado')?.addEventListener('input', actualizarProgresoPerfil);
        
        // Manejo de áreas
        document.getElementById('area')?.addEventListener('change', this.manejarCambioArea.bind(this));
    }

    toggleFiltros = () => {
        const filtros = document.getElementById('filtrosAvanzados');
        if (filtros) {
            filtros.style.display = filtros.style.display === 'none' ? 'block' : 'none';
            const icon = document.getElementById('filtrosIcon');
            if (icon) {
                icon.className = filtros.style.display === 'none' ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
            }
        }
    }

    setCurrentDate() {
        const dateElement = document.getElementById('current-date');
        if (dateElement) {
            dateElement.textContent = new Date().toLocaleDateString('es-AR', {
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            });
        }
    }

    // Métodos de modal (ahora delegados a modales.js)
    abrirModalNuevo = () => abrirModalNuevo();
    abrirModalEditar = (id) => abrirModalEditar(id);
    iniciarBaja = (id) => iniciarBaja(id);
    mostrarCamposExtras = () => mostrarCamposExtras();

    // Manejo de áreas
    async manejarCambioArea(event) {
        const areaId = event.target.value;
        if (areaId === 'nueva_area') {
            const nombre = prompt('Nombre de la nueva área:');
            if (nombre) {
                try {
                    const nuevaArea = await crearNuevaArea(nombre);
                    await cargarAreas().then(areas => {
                        const select = document.getElementById('area');
                        if (select) {
                            select.innerHTML = areas.map(area => 
                                `<option value="${area.id}">${area.nombre}</option>`
                            ).join('') + '<option value="nueva_area">+ Crear Nueva Área</option>';
                            select.value = nuevaArea.id;
                            cargarCargos(nuevaArea.id);
                        }
                    });
                } catch (error) {
                    console.error('Error al crear área:', error);
                }
            } else {
                event.target.value = '';
            }
        } else {
            cargarCargos(areaId);
        }
    }

    // Guardar empleado
    async guardarEmpleado(event) {
        event.preventDefault();
        
        if (!validarFormulario(event)) return;

        try {
            const form = event.target;
            const formData = new FormData(form);

            await guardarEmpleadoAPI(formData);

            // Cerrar modal y recargar
            const modal = Modal.getInstance(document.getElementById('modalGestionEmpleado'));
            if (modal) modal.hide();
            
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error al guardar empleado:', error);
        }
    }
}

// Función para popular formulario (usada por modales.js)
export function popularFormulario(empleado) {
    const form = document.getElementById('formEmpleado');
    if (!form) return;

    const fieldMap = {
        'nombre': 'nombre',
        'apellido': 'apellido',
        'dni': 'dni',
        'fechaNacimiento': 'fecha_nacimiento',
        'grupoSanguineo': 'grupo_sanguineo',
        'telefono': 'telefono',
        'email': 'email',
        'direccion': 'direccion',
        'localidad': 'localidad',
        'provincia': 'provincia',
        'codigoPostal': 'codigo_postal',
        'alergias': 'alergias',
        'medicamentos': 'medicamentos',
        'condicionImpositiva': 'condicion_impositiva',
        'cbu': 'cbu',
        'cuentaBancaria': 'cuenta_bancaria',
        'banco': 'banco',
        'fechaIngreso': 'fecha_ingreso',
        'area': 'area',
        'cargo': 'cargo',
        'tipoContrato': 'tipo_contrato',
        'salarioBase': 'salario_base',
        'bonificaciones': 'bonificaciones',
        'obraSocial': 'obra_social',
        'numeroAfiliado': 'numero_afiliado',
        'contactoEmergenciaNombre': 'contacto_emergencia_nombre',
        'contactoEmergenciaTelefono': 'contacto_emergencia_telefono',
        'contactoEmergenciaParentesco': 'contacto_emergencia_parentesco',
        'categoriaMonotributo': 'categoria_monotributo',
        'fechaInicioActividad': 'fecha_inicio_actividad',
        'ingresosBrutos': 'ingresos_brutos'
    };

    Object.entries(fieldMap).forEach(([fieldId, empleadoField]) => {
        const element = document.getElementById(fieldId);
        if (element) {
            const value = empleado[empleadoField] || '';
            
            if (element.type === 'checkbox' || element.type === 'radio') {
                element.checked = element.value === value.toString();
            } else {
                element.value = value;
            }
        }
    });
}