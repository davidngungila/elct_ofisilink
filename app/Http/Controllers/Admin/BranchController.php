<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::with('managers')->orderBy('name')->get();
        $employees = User::with(['employee', 'primaryDepartment'])
            ->whereHas('employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.settings.branches.index', compact('branches', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = User::with(['employee', 'primaryDepartment'])
            ->whereHas('employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.branches.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_ids' => 'nullable|array',
            'manager_ids.*' => 'exists:users,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $branch = Branch::create($validated);

        // Sync managers
        if ($request->has('manager_ids')) {
            $branch->managers()->sync($request->manager_ids);
        }

        return redirect()->route('admin.settings.branches.page')
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $branch->load(['users' => function($query) {
            $query->with('primaryDepartment', 'roles')->orderBy('name');
        }, 'managers']);
        return view('admin.settings.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        $branch->load('managers');
        $employees = User::with(['employee', 'primaryDepartment'])
            ->whereHas('employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Return partial view for modal
        if (request()->expectsJson() || request()->ajax()) {
            return view('admin.settings.branches.partials.edit-form', compact('branch', 'employees'));
        }
        return view('admin.branches.edit', compact('branch', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_ids' => 'nullable|array',
            'manager_ids.*' => 'exists:users,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $branch->update($validated);

        // Sync managers
        if ($request->has('manager_ids')) {
            $branch->managers()->sync($request->manager_ids);
        } else {
            $branch->managers()->sync([]);
        }

        return redirect()->route('admin.settings.branches.page')
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        // Check if branch has users
        if ($branch->users()->count() > 0) {
            return redirect()->route('admin.settings.branches.page')
                ->with('error', 'Cannot delete branch with assigned users.');
        }

        $branch->delete();

        return redirect()->route('admin.settings.branches.page')
            ->with('success', 'Branch deleted successfully.');
    }
}
