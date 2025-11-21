<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentoConductor;
use App\Models\DocumentoVehiculo;
use App\Models\Alerta;
use App\Models\Usuario;
use App\Notifications\DocumentoVencidoNotification;
use Carbon\Carbon;
use DB;

class CheckDocumentExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:document-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa documentos próximos a vencer y vencidos, crea alertas y nitifica.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener la fecha actual y la fecha límite para próximos a vencer
        $hoy = Carbon::today();
        $proximo = $hoy->copy()->addDays(15); // Definir el rango de días para próximos a vencer

        //revisar documentos de vehículos
        $docsV = DocumentoVehiculo::whereNull('reemplazado_por')
            ->where('estado', '!=', 'REEMPLAZADO')
            ->where(function ($q) use ($hoy, $proximo) {
                $q->whereBetween('fecha_vencimiento', [$hoy, $proximo])
                    ->orWhere('fecha_vencimiento', '<', $hoy);
            })->get();



        //documentos de conductores
        $docsC = DocumentoConductor::whereNull('reemplazado_por')
            ->where('estado', '!=', 'REEMPLAZADO')
            ->where(function ($q) use ($hoy, $proximo) {
                $q->whereBetween('fecha_vencimiento', [$hoy, $proximo])
                    ->orWhere('fecha_vencimiento', '<', $hoy);
            })->get();

        $all = $docsV->concat($docsC);

        //crear alertas y notificar
        foreach ($all as $d) {
            $tipo_v = $d->fecha_vencimiento < $hoy ? 'VENCIDO' : 'POR VENCER';
            $mensaje = sprintf("Documento %s (%s) - vence: %s", $d->tipo_documento, $d->numero_documento, $d->fecha_vencimiento ? $d->fecha_vencimiento->format('Y-m-d') : 'Sin fecha');

            $alert = Alerta::create([
                'tipo_alerta' => $d instanceof DocumentoVehiculo ? 'VEHICULO' : 'CONDUCTOR',
                'id_doc_vehiculo' => $d instanceof DocumentoVehiculo ? $d->id_doc_vehiculo : null,
                'id_doc_conductor' => $d instanceof DocumentoConductor ? $d->id_doc_conductor : null,
                'tipo_vencimiento' => $tipo_v,
                'mensaje' => $mensaje,
                'fecha_alerta' => Carbon::today(),
                'leida' => 0,
                'visible_para' => 'TODOS',
                'creado_por' => null,
            ]);

            // Notificar a usuarios por rol
            $users = Usuario::whereIn('rol', ['ADMIN', 'SST', 'PORTERIA'])->where('activo', 1)->get();

            foreach ($users as $u) {
                $u->notify(new DocumentoVencidoNotification($alert));
            }
        }

        $this->info('Check completed - alerts created: ' . $all->count());
        return 0;
    }
}
