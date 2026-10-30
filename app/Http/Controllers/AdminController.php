<?php

namespace App\Http\Controllers;

use App\Domain\AdminWorkspace;
use Illuminate\Http\Request;

final class AdminController
{
    public function manage(Request $r, AdminWorkspace $workspace)
    {
        $result = $workspace->operate($r);
        if ($r->expectsJson()) {
            return response()->json($result);
        }

return redirect('/admin/workspace?'.http_build_query(['section' => $r->input('section'), 'edit' => $result['id'] ?? $r->input('id'), 'locale' => $r->input('locale', 'en')]))->with('status', 'Operation completed.')->with('simulation', $result['simulation'] ?? null);
    }
}
