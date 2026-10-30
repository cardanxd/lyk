<?php

it('muestra la home con el logo y el lema', function () {
    $this->get('/')->assertOk()
        ->assertSee('images/brand/logo.png')
        ->assertSee('Hacemos de tu retiro una nueva oportunidad');
});
