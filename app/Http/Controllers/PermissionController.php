<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

use App\Traits\ApiResponseTrait;

class PermissionController extends Controller
{
    use ApiResponseTrait;

    /**
     * عرض صفحة إدارة الصلاحيات
     */
    public function index(): View
    {
        try {
        
            $permissions = Permission::with('designations')
                ->orderBy('module')
                ->orderBy('action')
                ->get()
                ->groupBy('module');

            $designations = Designation::with('permissions')->get();
            $users = User::with(['designation', 'designation.permissions'])->get();

            return view('permissions.index', compact('permissions', 'designations', 'users'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * عرض صفحة تعديل صلاحيات الوظيفة
     */
    public function editDesignationPermissions(Designation $designation): View
    {
        $designation->load('permissions');
        
        $permissions = Permission::orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module');

        return view('permissions.edit-designation', compact('designation', 'permissions'));
    }

    /**
     * تحديث صلاحيات الوظيفة
     */
    public function updateDesignationPermissions(Request $request, Designation $designation): JsonResponse
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // مدير النظام لديه كل الصلاحيات دائماً
        if ($designation->name === 'مدير نظام') {
            return response()->json([
                'message' => 'لا يمكن تعديل صلاحيات مدير النظام',
                'type' => 'error'
            ], 403);
        }

        $designation->permissions()->sync($request->permissions);

        return response()->json([
            'message' => 'تم تحديث الصلاحيات بنجاح',
            'type' => 'success'
        ]);
    }

    /**
     * عرض صلاحيات المستخدم
     */
    public function userPermissions(User $user): JsonResponse
    {
        $permissions = $user->isSystemAdmin() 
            ? Permission::selectRaw('id, COALESCE(description, name) as display_name')->pluck('display_name', 'id')
            : $user->designation?->permissions()
                ->selectRaw('permissions.id, COALESCE(permissions.description, permissions.name) as display_name')
                ->pluck('display_name', 'permissions.id') ?? [];

        return $this->successResponse([
            'user' => $user->load('designation'),
            'permissions' => $permissions,
            'is_admin' => $user->isSystemAdmin()
        ]);
    }

    /**
     * الحصول على كل الصلاحيات
     */
    public function getAllPermissions(): JsonResponse
    {
        try {
            $permissions = Permission::orderBy('module')
                ->orderBy('action')
                ->get()
                ->groupBy('module');

            return $this->successResponse($permissions);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'حدث خطأ في جلب الصلاحيات',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * إنشاء صلاحية جديدة
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
            'description' => 'nullable|string',
            'module' => 'required|string',
            'action' => 'required|string',
        ]);

        $permission = Permission::create($request->all());

        return response()->json([
            'message' => 'تم إنشاء الصلاحية بنجاح',
            'permission' => $permission
        ]);
    }

    /**
     * تحديث الصلاحية
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
            'module' => 'required|string',
            'action' => 'required|string',
        ]);

        $permission->update($request->all());

        return response()->json([
            'message' => 'تم تحديث الصلاحية بنجاح',
            'permission' => $permission
        ]);
    }

    /**
     * حذف الصلاحية
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json([
            'message' => 'تم حذف الصلاحية بنجاح'
        ]);
    }
    /**
     * الحصول على كل الوظائف (Designations)
     */
    public function getDesignations(Request $request): JsonResponse
    {
        try {
            $query = Designation::withCount('permissions');
            
            if ($request->has('with') && $request->with === 'permissions') {
                $query->with('permissions');
            }
            
            $designations = $query->get();
            return $this->successResponse($designations);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'حدث خطأ في جلب الوظائف');
        }
    }
    /**
     * الحصول على بيانات وظيفة واحدة مع صلاحياتها
     */
    public function showDesignation(Designation $designation): JsonResponse
    {
        return $this->successResponse($designation->load('permissions'));
    }
}
