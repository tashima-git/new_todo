<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;

class PlanController extends Controller
{
    /**
     * プラン確認画面
     * GET /plan
     */
    public function index()
    {
        $user = Auth::user();

        // プラン一覧（選択肢として表示）
        $plans = Plan::query()
            ->orderBy('id')
            ->get();

        // 現在のプラン（nullの可能性もある）
        $currentPlan = $user->plan;

        return view('plan.index', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
        ]);
    }

    /**
     * プラン更新
     * POST /plan
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        // 変更が無いならそのまま戻す（余計な更新をしない）
        if ((int)$user->plan_id === (int)$validated['plan_id']) {
            return redirect()
                ->route('plan.index')
                ->with('success', 'プランはすでに選択されています。');
        }

        $user->plan_id = $validated['plan_id'];
        $user->save();

        return redirect()
            ->route('plan.index')
            ->with('success', 'プランを変更しました。');
    }
}
