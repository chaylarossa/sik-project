<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCrisisTypeRequest;
use App\Http\Requests\Admin\UpdateCrisisTypeRequest;
use App\Models\CrisisType;
use Illuminate\Http\Request;

class CrisisTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CrisisType::class, 'crisis_type');
    }

    public function index(Request $request)
    {
        $search = $request->string('search')->trim();

        $crisisTypes = CrisisType::query()
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.crisis-types.index', [
            'crisisTypes' => $crisisTypes,
            'search' => $search->toString(),
        ]);
    }

    public function create()
    {
        return view('admin.crisis-types.create');
    }

    public function store(StoreCrisisTypeRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        CrisisType::create($data);

        return redirect()
            ->route('admin.crisis-types.index')
            ->with('status', 'Jenis krisis berhasil ditambahkan.');
    }

    public function edit(CrisisType $crisisType)
    {
        return view('admin.crisis-types.edit', [
            'crisisType' => $crisisType,
        ]);
    }

    public function update(UpdateCrisisTypeRequest $request, CrisisType $crisisType)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $crisisType->update($data);

        return redirect()
            ->route('admin.crisis-types.index')
            ->with('status', 'Jenis krisis berhasil diperbarui.');
    }

    public function destroy(CrisisType $crisisType)
    {
        $crisisType->delete();

        return redirect()
            ->route('admin.crisis-types.index')
            ->with('status', 'Jenis krisis berhasil dihapus.');
    }
}
