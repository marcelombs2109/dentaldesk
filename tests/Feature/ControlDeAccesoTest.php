<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Comprobaciones básicas del control de acceso (SDD-01, SDD-02, SDD-04).
 */
class ControlDeAccesoTest extends TestCase
{
    public function test_la_raiz_redirige_al_login_sin_sesion(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_las_rutas_de_pacientes_exigen_sesion(): void
    {
        $this->get('/pacientes')->assertRedirect('/login');
        $this->get('/agenda')->assertRedirect('/login');
    }

    public function test_el_login_muestra_el_formulario(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_el_webhook_rechaza_peticiones_sin_firma(): void
    {
        $this->post('/webhook/whatsapp', [
            'From' => 'whatsapp:+59174272446',
            'Body' => 'NO',
        ])->assertStatus(403);
    }
}
