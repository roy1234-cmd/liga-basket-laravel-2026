<?php

namespace App\Http\Controllers;

use App\Services\LigaStore;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LigaController extends Controller
{
    public function __construct(private LigaStore $store) {}
    public function estado() { return response()->json($this->store->read()); }

    private function error(string $message): never
    {
        throw ValidationException::withMessages(['error' => $message]);
    }

    private function requireItem(array $items, string $id): void
    {
        if (!collect($items)->contains('id', $id)) abort(404, 'Registro no encontrado.');
    }

    private function teamExists(array $state, string $id): bool
    {
        return collect($state['equipos'])->contains('id', $id);
    }

    public function crearEquipo(Request $request)
    {
        $input = $request->validate(['nombre'=>'required|string|min:2|max:60','entrenador'=>'nullable|string|max:60','ciudad'=>'nullable|string|max:60']);
        return response()->json($this->store->update(function (&$state) use ($input) {
            if (collect($state['equipos'])->contains(fn ($t) => mb_strtolower($t['nombre']) === mb_strtolower(trim($input['nombre'])))) $this->error('El equipo ya existe.');
            $state['equipos'][] = ['id'=>(string) Str::uuid(),'nombre'=>trim($input['nombre']), 'entrenador'=>trim($input['entrenador'] ?? ''), 'ciudad'=>trim($input['ciudad'] ?? '')];
        }));
    }

    public function editarEquipo(Request $request, string $id)
    {
        $input = $request->validate(['nombre'=>'required|string|min:2|max:60','entrenador'=>'nullable|string|max:60','ciudad'=>'nullable|string|max:60']);
        return response()->json($this->store->update(function (&$state) use ($input,$id) {
            $this->requireItem($state['equipos'], $id);
            if (collect($state['equipos'])->contains(fn ($t) => $t['id'] !== $id && mb_strtolower($t['nombre']) === mb_strtolower(trim($input['nombre'])))) $this->error('Otro equipo ya tiene ese nombre.');
            foreach ($state['equipos'] as &$team) if ($team['id'] === $id) $team = array_merge($team, ['nombre'=>trim($input['nombre']), 'entrenador'=>trim($input['entrenador'] ?? ''),'ciudad'=>trim($input['ciudad'] ?? '')]);
            unset($team);
        }));
    }

    public function eliminarEquipo(string $id)
    {
        return response()->json($this->store->update(function (&$state) use ($id) {
            $this->requireItem($state['equipos'], $id);
            $state['equipos'] = array_values(array_filter($state['equipos'], fn ($t) => $t['id'] !== $id));
            $state['jugadores'] = array_values(array_filter($state['jugadores'], fn ($j) => $j['equipo'] !== $id));
            $state['partidos'] = array_values(array_filter($state['partidos'], fn ($p) => $p['local'] !== $id && $p['visitante'] !== $id));
        }));
    }

    private function checkPlayer(array $state, array $input, ?string $id = null): void
    {
        if (!$this->teamExists($state, $input['equipo'])) $this->error('Selecciona un equipo válido.');
        if (collect($state['jugadores'])->contains(fn ($j) => $j['id'] !== $id && $j['equipo'] === $input['equipo'] && (int)$j['numero'] === (int)$input['numero'])) {
            $this->error('Ese número de camiseta ya está ocupado en el equipo.');
        }
    }

    public function crearJugador(Request $request)
    {
        $input = $request->validate(['nombre'=>'required|string|min:2|max:80','numero'=>'required|integer|min:0|max:99','equipo'=>'required|string']);
        return response()->json($this->store->update(function (&$state) use ($input) {
            $this->checkPlayer($state,$input);
            $state['jugadores'][] = array_merge(['id'=>(string)Str::uuid()],$input,['nombre'=>trim($input['nombre']),'numero'=>(int)$input['numero']]);
        }));
    }

    public function editarJugador(Request $request, string $id)
    {
        $input = $request->validate(['nombre'=>'required|string|min:2|max:80','numero'=>'required|integer|min:0|max:99','equipo'=>'required|string']);
        return response()->json($this->store->update(function (&$state) use ($input,$id) {
            $this->requireItem($state['jugadores'],$id);
            $this->checkPlayer($state,$input,$id);
            foreach ($state['jugadores'] as &$j) if ($j['id'] === $id) $j = array_merge($j,$input,['nombre'=>trim($input['nombre']),'numero'=>(int)$input['numero']]);
            unset($j);
        }));
    }

    public function eliminarJugador(string $id)
    {
        return response()->json($this->store->update(function (&$state) use ($id) {
            $this->requireItem($state['jugadores'],$id);
            $state['jugadores'] = array_values(array_filter($state['jugadores'], fn ($j) => $j['id'] !== $id));
        }));
    }

    private function checkMatch(array $state,array $p): void
    {
        if ($p['local'] === $p['visitante']) $this->error('Debes elegir equipos diferentes.');
        if (!$this->teamExists($state,$p['local']) || !$this->teamExists($state,$p['visitante'])) $this->error('Selecciona equipos registrados.');
    }

    public function crearPartido(Request $request)
    {
        $input = $request->validate(['local'=>'required|string','visitante'=>'required|string','fecha'=>'required|date_format:Y-m-d']);
        return response()->json($this->store->update(function (&$state) use ($input) {
            $this->checkMatch($state,$input);
            $state['partidos'][] = array_merge(['id'=>(string)Str::uuid()],$input,['estado'=>'programado']);
        }));
    }

    public function editarPartido(Request $request,string $id)
    {
        $input = $request->validate(['local'=>'sometimes|required|string','visitante'=>'sometimes|required|string',
            'fecha'=>'sometimes|required|date_format:Y-m-d','estado'=>'sometimes|in:programado,finalizado',
            'puntosLocal'=>'sometimes|required|integer|min:0|max:300','puntosVisitante'=>'sometimes|required|integer|min:0|max:300']);
        return response()->json($this->store->update(function (&$state) use ($id,$input) {
            $this->requireItem($state['partidos'],$id);
            foreach ($state['partidos'] as &$match) {
                if ($match['id'] !== $id) continue;
                $updated = array_merge($match,$input);
                $this->checkMatch($state,$updated);
                if ($updated['local'] !== $match['local'] || $updated['visitante'] !== $match['visitante']) {
                    unset($updated['puntosLocal'],$updated['puntosVisitante']);
                    $updated['estado'] = 'programado';
                }
                if ($updated['estado'] === 'finalizado') {
                    if (!isset($updated['puntosLocal'],$updated['puntosVisitante'])) $this->error('Faltan marcadores.');
                    if ((int)$updated['puntosLocal'] === (int)$updated['puntosVisitante']) $this->error('Un partido finalizado no puede empatar.');
                } else unset($updated['puntosLocal'],$updated['puntosVisitante']);
                $match = $updated;
            }
            unset($match);
        }));
    }

    public function eliminarPartido(string $id)
    {
        return response()->json($this->store->update(function (&$state) use ($id) {
            $this->requireItem($state['partidos'],$id);
            $state['partidos'] = array_values(array_filter($state['partidos'], fn ($p) => $p['id'] !== $id));
        }));
    }

    public function exportar()
    {
        return response()->json($this->store->read(),200,['Content-Disposition'=>'attachment; filename="liga-basket-respaldo.json"','Cache-Control'=>'no-store'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function importar(Request $request)
    {
        $request->validate(['archivo'=>'required|file|max:2048']);
        $import = json_decode(file_get_contents($request->file('archivo')->getRealPath()), true);
        if (!is_array($import)) $this->error('El archivo no contiene JSON válido.');
        $validator = Validator::make($import,[
            'equipos'=>'required|array|max:500','equipos.*.id'=>'required|string|distinct','equipos.*.nombre'=>'required|string|max:60',
            'equipos.*.entrenador'=>'nullable|string|max:60','equipos.*.ciudad'=>'nullable|string|max:60',
            'jugadores'=>'required|array|max:10000','jugadores.*.id'=>'required|string|distinct','jugadores.*.nombre'=>'required|string|max:80',
            'jugadores.*.numero'=>'required|integer|min:0|max:99','jugadores.*.equipo'=>'required|string',
            'partidos'=>'required|array|max:20000','partidos.*.id'=>'required|string|distinct',
            'partidos.*.local'=>'required|string','partidos.*.visitante'=>'required|string',
            'partidos.*.fecha'=>'required|date_format:Y-m-d','partidos.*.estado'=>'required|in:programado,finalizado',
            'partidos.*.puntosLocal'=>'nullable|integer|min:0|max:300','partidos.*.puntosVisitante'=>'nullable|integer|min:0|max:300',
        ]);
        if ($validator->fails()) throw new ValidationException($validator);
        $teamIds = array_column($import['equipos'],'id');
        foreach ($import['jugadores'] as $j) if (!in_array($j['equipo'],$teamIds,true)) $this->error('Jugador con equipo inexistente.');
        foreach ($import['partidos'] as $p) {
            if (!in_array($p['local'],$teamIds,true) || !in_array($p['visitante'],$teamIds,true) || $p['local'] === $p['visitante']) $this->error('Partido con equipos inválidos.');
            if ($p['estado'] === 'finalizado' && (!isset($p['puntosLocal'],$p['puntosVisitante']) || $p['puntosLocal'] === $p['puntosVisitante'])) $this->error('Partido finalizado sin resultado válido.');
        }
        return response()->json($this->store->update(function (&$state) use ($import) {
            $state = ['equipos'=>$import['equipos'],'jugadores'=>$import['jugadores'],'partidos'=>$import['partidos']];
        }));
    }
}
