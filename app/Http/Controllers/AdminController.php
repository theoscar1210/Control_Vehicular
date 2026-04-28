<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function about()
    {
        if (Auth::user()->rol !== 'ADMIN') {
            abort(403);
        }

        $lastCommit = trim(shell_exec('git log -1 --format="%h %s" 2>/dev/null') ?? 'No disponible');

        $gitLog = shell_exec('git log -10 --format="%h|%s|%ad" --date=format:"%d/%m/%Y" 2>/dev/null');
        $commits = [];
        if ($gitLog) {
            foreach (explode("\n", trim($gitLog)) as $line) {
                $parts = explode('|', $line, 3);
                if (count($parts) === 3) {
                    $commits[] = [
                        'hash'    => $parts[0],
                        'mensaje' => $parts[1],
                        'fecha'   => $parts[2],
                    ];
                }
            }
        }

        return view('admin.about', compact('lastCommit', 'commits'));
    }
}
