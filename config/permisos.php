<?php

/*
|--------------------------------------------------------------------------
| Matriz de permisos (SDD-02)
|--------------------------------------------------------------------------
| Única fuente de verdad del RBAC. Cada permiso lista los roles que pueden
| ejecutarlo; cualquier rol ausente queda denegado (mínimo privilegio).
| Cambiar esta matriz cambia el comportamiento sin tocar controladores.
*/

return [

    // Pacientes
    'ver_pacientes'        => ['administrador', 'recepcionista', 'odontologo'],
    'registrar_paciente'   => ['administrador', 'recepcionista'],
    'modificar_paciente'   => ['administrador', 'recepcionista'],
    'borrar_paciente'      => ['administrador'],

    // Agenda y citas
    'ver_agenda'              => ['administrador', 'recepcionista', 'odontologo'],
    'consultar_disponibilidad' => ['administrador', 'recepcionista', 'odontologo'],
    'crear_cita'              => ['administrador', 'recepcionista'],
    'ver_cita'                => ['administrador', 'recepcionista', 'odontologo'],
    'cambiar_estado_cita'     => ['administrador', 'recepcionista', 'odontologo'],

    // Administración (sin pantalla todavía; se documenta como trabajo futuro)
    'gestionar_usuarios'   => ['administrador'],
    'configurar_catalogos' => ['administrador'],

];
