<?php

namespace Modules\Admin\Http\Controllers\Traits;

use App\Models\Traits\SortableTrait;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Http\Requests\SortRequest;

/**
 * @property SortableTrait|string $modelClass
 */
trait SortableController
{
    public function sort(SortRequest $request): RedirectResponse
    {
        $order = $request->input('order');
        \DB::transaction(fn() => $this->modelClass::setNewOrder($order));
        return back()->with(['message' => "Вопросы были отсортированы."]);
    }
}
