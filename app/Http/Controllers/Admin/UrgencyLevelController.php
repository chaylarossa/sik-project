<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUrgencyLevelRequest;
use App\Http\Requests\Admin\UpdateUrgencyLevelRequest;
use App\Models\UrgencyLevel;
use Illuminate\Http\Request;

class UrgencyLevelController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UrgencyLevel::class, 'urgency_level');
    }

    public function index(Request $request)
    {
        $search = $request->string('search')->trim();

        $urgencyLevels = UrgencyLevel::query()
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('color', 'like', '%'.$search.'%');

                    if (is_numeric($search->toString())) {
                        $builder->orWhere('level', (int) $search->toString());
                    }
                });
            })
            ->orderBy('level')
            ->paginate(10)
            ->withQueryString();

        return view('admin.urgency-levels.index', [
            'urgencyLevels' => $urgencyLevels,
            'search' => $search->toString(),
        ]);
    }

    public function create()
    {
        return view('admin.urgency-levels.create');
    }

    public function store(StoreUrgencyLevelRequest $request)
    {
        $data = $request->validated();
        $data['is_high_priority'] = $request->boolean('is_high_priority');

        UrgencyLevel::create($data);

        return redirect()
            ->route('admin.urgency-levels.index')
            ->with('status', 'Tingkat urgensi berhasil ditambahkan.');
    }

    public function edit(UrgencyLevel $urgencyLevel)
    {
        return view('admin.urgency-levels.edit', [
            'urgencyLevel' => $urgencyLevel,
        ]);
    }

    public function update(UpdateUrgencyLevelRequest $request, UrgencyLevel $urgencyLevel)
    {
        $data = $request->validated();
        $data['is_high_priority'] = $request->boolean('is_high_priority');

        $urgencyLevel->update($data);

        return redirect()
            ->route('admin.urgency-levels.index')
            ->with('status', 'Tingkat urgensi berhasil diperbarui.');
    }

    public function destroy(UrgencyLevel $urgencyLevel)
    {
        $urgencyLevel->delete();

        return redirect()
            ->route('admin.urgency-levels.index')
            ->with('status', 'Tingkat urgensi berhasil dihapus.');
    }
}
