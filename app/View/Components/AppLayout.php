<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Obtener la vista/el contenido que representa el componente.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
