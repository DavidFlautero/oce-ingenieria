<!-- Modal Nuevo/Editar Empleado COMPLETO -->
<!-- Cambiar el div del modal (línea 1) -->
<div class="modal fade" id="modalGestionEmpleado" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true" data-bs-backdrop="static">    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <span id="modalTitle"><i class="fas fa-user-plus me-2"></i>Nuevo Empleado</span>
                    <span id="estadoPerfilModal" class="badge bg-success ms-2">Completo</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="formEmpleado" onsubmit="guardarEmpleado(event)" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="empleadoId" name="id">
                    
                    <!-- Barra de progreso -->
                    <div class="progress progress-thin mb-4">
                        <div class="progress-bar bg-success" id="progresoPerfil" role="progressbar" style="width: 100%"></div>
                    </div>
                    
                    <!-- Pestañas -->
                    <ul class="nav nav-tabs mb-3" id="empleadoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos-personales" type="button">Datos Personales</button>
                        </li>
                      
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="laboral-tab" data-bs-toggle="tab" data-bs-target="#datos-laborales" type="button">Datos Laborales</button>
                        </li>
						  <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentacion" type="button">Documentación</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="emergencia-tab" data-bs-toggle="tab" data-bs-target="#emergencia" type="button">Contacto Emergencia</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="empleadoTabContent">
                        <!-- Pestaña Datos Personales -->
                        <div class="tab-pane fade show active" id="datos-personales" role="tabpanel">
                            <div class="row g-3">
                                <!-- Sección Identificación -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">DNI</label>
                                    <input type="text" class="form-control" id="dni" name="dni" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">CUIT/CUIL</label>
                                    <input type="text" class="form-control" id="cuitCuil" name="cuit_cuil" pattern="[0-9]{11}" placeholder="Ej: 20123456789" required>
                                    <small class="text-muted">11 dígitos sin guiones</small>
                                </div>
                                
                                <!-- Sección Datos Personales -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">Fecha Nacimiento</label>
                                    <input type="date" class="form-control" id="fechaNacimiento" name="fecha_nacimiento" placeholder="DD/MM/AAAA" 
       pattern="\d{2}/\d{2}/\d{4}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Grupo Sanguíneo</label>
                                    <select class="form-select" id="grupoSanguineo" name="grupo_sanguineo" >
                                        <option value="">Seleccionar...</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="A Rh+">A Rh+</option>
                                        <option value="A Rh-">A Rh-</option>
                                        <option value="B Rh+">B Rh+</option>
                                        <option value="B Rh-">B Rh-</option>
                                        <option value="AB Rh+">AB Rh+</option>
                                        <option value="AB Rh-">AB Rh-</option>
                                        <option value="O Rh+">O Rh+</option>
                                        <option value="O Rh-">O Rh-</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alergias/Enfermedades</label>
                                    <textarea class="form-control" id="alergias" name="alergias" rows="2"></textarea>
                                </div>
                                
                                <!-- Sección Contacto -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">Teléfono/Celular</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="1157633254"required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="oce@gmail.com" >
                                </div>
                                
                                <!-- Sección Domicilio -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Olavarria 600" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Barrio/Partido</label>
                                    <input type="text" class="form-control" name="localidad" placeholder="MonteGrande- Esteban Echeverria" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Provincia</label>
                                    <select class="form-select" name="provincia" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Buenos Aires">Buenos Aires</option>
                                        <option value="CABA">Ciudad Autónoma de Buenos Aires</option>
                                        <option value="Catamarca">Catamarca</option>
                                        <option value="Chaco">Chaco</option>
                                        <option value="Chubut">Chubut</option>
                                        <option value="Córdoba">Córdoba</option>
                                        <option value="Corrientes">Corrientes</option>
                                        <option value="Entre Ríos">Entre Ríos</option>
                                        <option value="Formosa">Formosa</option>
                                        <option value="Jujuy">Jujuy</option>
                                        <option value="La Pampa">La Pampa</option>
                                        <option value="La Rioja">La Rioja</option>
                                        <option value="Mendoza">Mendoza</option>
                                        <option value="Misiones">Misiones</option>
                                        <option value="Neuquén">Neuquén</option>
                                        <option value="Río Negro">Río Negro</option>
                                        <option value="Salta">Salta</option>
                                        <option value="San Juan">San Juan</option>
                                        <option value="San Luis">San Luis</option>
                                        <option value="Santa Cruz">Santa Cruz</option>
                                        <option value="Santa Fe">Santa Fe</option>
                                        <option value="Santiago del Estero">Santiago del Estero</option>
                                        <option value="Tierra del Fuego">Tierra del Fuego</option>
                                        <option value="Tucumán">Tucumán</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pestaña Documentación -->
                        <div class="tab-pane fade" id="documentacion" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Todos los documentos deben estar vigentes y ser claramente legibles
                            </div>
                            <div class="row g-3">
                                <!-- Documentos Obligatorios -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">DNI (Frente)</label>
                                    <input type="file" class="form-control" id="dniFrente" name="foto_dni_frente" accept="image/*,application/pdf">
                                    <small class="text-muted">Foto o escaneo frontal legible</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">DNI (Dorso)</label>
                                    <input type="file" class="form-control" id="dniDorso" name="foto_dni_dorso" accept="image/*,application/pdf">
                                    <small class="text-muted">Foto o escaneo dorso legible</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">CBU/CVU</label>
                                    <input type="text" class="form-control" id="cbu" name="cbu" pattern="[0-9]{22}">
                                    <small class="text-muted">22 dígitos sin espacios</small>
                                </div>
                                
                                <!-- Documentos Opcionales -->
                                <div class="col-md-6">
                                    <label class="form-label">Registro de Conducir</label>
                                    <div class="input-group mb-2">
                                        <input type="file" class="form-control" id="registroConducir" name="registro_conducir" accept="image/*,application/pdf">
                                        <input type="date" class="form-control" id="vencimientoRegistro" name="vencimiento_registro" placeholder="Vencimiento">
                                    </div>
                                    <small class="text-muted">Opcional - Cargar si posee</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Certificado Médico</label>
                                    <input type="file" class="form-control" id="certificadoMedico" name="certificado_medico" accept="image/*,application/pdf">
                                    <small class="text-muted">Recomendado para roles de riesgo</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Otros Documentos</label>
                                    <input type="file" class="form-control" id="otrosDocumentos" name="otros_documentos[]" multiple>
                                    <small class="text-muted">Títulos, certificaciones, etc.</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pestaña Datos Laborales -->
                        <div class="tab-pane fade" id="datos-laborales" role="tabpanel">
                            <div class="row g-3">
                                <!-- Datos Básicos Laborales -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">Fecha Ingreso</label>
                                    <input type="date" class="form-control" id="fechaIngreso" name="fecha_ingreso" required
                                           min="{{ now()->subYears(10)->format('Y-m-d') }}"
                                           max="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Área</label>
                                    <select class="form-select" id="area" name="area_id" required onchange="mostrarInputNuevaArea(this)">
                                        <option value="">Seleccionar...</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                        @endforeach
                                        <option value="nueva_area">+ Crear Nueva Área</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="input-nueva-area" style="display: none;">
                                    <label class="form-label required-field">Nombre Nueva Área</label>
                                    <input type="text" name="nueva_area" class="form-control" 
                                           placeholder="Ingrese nueva área" id="nueva-area-input">
                                    <small class="text-muted">El área se creará automáticamente al guardar</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Condicion Laboral</label>
                                    <select class="form-select" id="relacionLaboral" name="relacion_laboral" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="planta">Planta permanente</option>
                                        <option value="prueba">Período de prueba</option>
                                        <option value="monotributista">Monotributista</option>
                                    </select>
                                </div>
                                
                                <!-- Datos Remunerativos -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">Salario Base</label>
                                    <input type="number" class="form-control" id="salarioBase" name="salario_base">
                                </div>
                               
                            </div>

                            <!-- Bloque Monotributo -->
                            <div id="seccionMonotributo" class="d-none mt-4">
                                <h5>Datos Monotributo</h5>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Estado Monotributo</label>
                                        <select class="form-select" id="estadoMonotributo" name="estado_monotributo">
                                            <option value="existente">Monotributista ( Ajeno )</option> 
                                            <option value="crear">Monotributista ( Gestionado por OCE )</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Categoría</label>
                                        <select class="form-select" id="categoria_monotributo" name="categoria_monotributo">
                                            <option value="">Seleccionar...</option>
                                            @foreach(range('A', 'K') as $letra)
                                                <option value="{{ $letra }}">{{ $letra }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Clave Fiscal </label>
                                        <input type="text" class="form-control" id="claveFiscal" name="clave_fiscal" placeholder="Opcional(Si es Ajeno)">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha Inscripción</label>
                                        <input type="date" class="form-control" id="fechaInscripcion" name="fecha_inscripcion_monotributo">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pestaña Contacto Emergencia -->
                        <div class="tab-pane fade" id="emergencia" role="tabpanel">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required-field">Nombre Contacto</label>
            <input type="text" class="form-control" id="contacto_emergencia_nombre" name="contacto_emergencia_nombre">
        </div>
        <div class="col-md-6">
            <label class="form-label required-field">Teléfono Contacto</label>
            <input type="tel" class="form-control" id="contacto_emergencia_telefono" name="contacto_emergencia_telefono">
        </div>
        <div class="col-md-6">
            <label class="form-label">Parentesco</label>
            <input type="text" class="form-control" id="contacto_emergencia_parentesco" name="contacto_emergencia_parentesco">
        </div>
        <div class="col-md-6">
            <label class="form-label">Otras Observaciones</label>
            <input type="text" class="form-control" id="contacto_emergencia_observaciones" name="contacto_emergencia_observaciones">
        </div>
    </div>
</div>
                            
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" id="btnEliminar" style="display: none;">
                    <i class="fas fa-trash me-1"></i>Dar de Baja
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEmpleado" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script para Monotributo -->
<!-- Scripts optimizados - Reemplazar desde @push('scripts') hasta @endpush -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monotributo Show/Hide
    const relacionLaboral = document.getElementById('relacionLaboral');
    if (relacionLaboral) {
        relacionLaboral.addEventListener('change', function() {
            const seccion = document.getElementById('seccionMonotributo');
            if (this.value === 'monotributista') {
                seccion.classList.remove('d-none');
            } else {
                seccion.classList.add('d-none');
            }
        });
    }

    // 2. Nueva Área Logic
    function mostrarInputNuevaArea(select) {
        const inputContainer = document.getElementById('input-nueva-area');
        const nuevaAreaInput = document.getElementById('nueva-area-input');
        
        if (!inputContainer || !nuevaAreaInput) return;
        
        if (select.value === 'nueva_area') {
            inputContainer.style.display = 'block';
            nuevaAreaInput.required = true;
            nuevaAreaInput.focus();
        } else {
            inputContainer.style.display = 'none';
            nuevaAreaInput.required = false;
        }
    }

    // 3. Date Format Handling (Versión Mejorada)
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.addEventListener('focus', (e) => {
            if (e.target.value && e.target.value.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const [year, month, day] = e.target.value.split('-');
                e.target.value = `${day}/${month}/${year}`;
            }
        });

        input.addEventListener('blur', (e) => {
            if (e.target.value.includes('/')) {
                const [day, month, year] = e.target.value.split('/');
                if (day && month && year) {
                    e.target.value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                }
            }
        });
    });

    // Inicialización del select de área
    const areaSelect = document.getElementById('area');
    if (areaSelect) {
        areaSelect.addEventListener('change', function() {
            mostrarInputNuevaArea(this);
        });
    }
});
</script>
@endpush