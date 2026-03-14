<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">إدارة الصلاحيات</h1>
      <p class="text-gray-600">إدارة صلاحيات الوظائف والمستخدمين في النظام</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md">
      <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === tab.key
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <div class="p-6">
        <div v-if="activeTab === 'overview'" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="designation in designations"
              :key="designation.id"
              class="border border-gray-100 rounded-xl p-5 hover:shadow-lg transition-all duration-300 bg-gradient-to-br from-white to-gray-50"
            >
              <h3 class="font-bold text-xl mb-2 text-gray-800">{{ designation.name }}</h3>
              <p class="text-sm text-gray-500 mb-4 flex items-center">
                <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold ml-2">
                  {{ designation.permissions_count }} صلاحية
                </span>
              </p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="permission in designation.permissions?.slice(0, 5)"
                  :key="permission.id"
                  class="bg-white border border-gray-200 text-gray-700 text-xs px-2 py-1 rounded-md shadow-sm"
                >
                  {{ permission.module }}.{{ permission.action }}
                </span>
                <span
                  v-if="designation.permissions?.length > 5"
                  class="text-xs text-gray-400 self-center"
                >
                  +{{ designation.permissions.length - 5 }} أخرى
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Designations Tab -->
        <div v-if="activeTab === 'designations'" class="space-y-4">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    الوظيفة
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    الصلاحيات
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    إجراءات
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="designation in designations" :key="designation.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ designation.name }}</div>
                    <div class="text-sm text-gray-500">المستوى: {{ designation.hierarchy_level }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">
                      {{ designation.permissions_count }} صلاحية
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <button
                      v-if="designation.name !== 'مدير نظام'"
                      @click="editDesignationPermissions(designation)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      تعديل الصلاحيات
                    </button>
                    <span v-else class="text-gray-400">
                      مدير النظام (كل الصلاحيات)
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Users Tab -->
        <div v-if="activeTab === 'users'" class="space-y-4">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    المستخدم
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    الوظيفة
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    الصلاحيات
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    إجراءات
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in users" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ user.designation?.name || 'غير محدد' }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span
                      v-if="user.is_admin"
                      class="text-sm font-medium text-green-600"
                    >
                      كل الصلاحيات
                    </span>
                    <span v-else class="text-sm text-gray-900 font-bold">
                      {{ user.permissions_count || 0 }} صلاحية
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <button
                      @click="showUserPermissions(user)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      عرض الصلاحيات
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Manage Tab -->
        <div v-if="activeTab === 'manage'" class="space-y-6">
          <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">قائمة الصلاحيات المتاحة</h2>
            <button
              @click="showCreatePermissionForm = true"
              class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition-all shadow-md active:scale-95"
            >
              إضافة صلاحية جديدة
            </button>
          </div>

          <div class="space-y-8">
            <div
              v-for="(modulePermissions, module) in permissions"
              :key="module"
              class="bg-gray-50 rounded-xl p-6 border border-gray-100"
            >
              <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                <div class="w-2 h-6 bg-blue-500 rounded-full ml-3"></div>
                <h4 class="font-bold text-lg text-gray-800">{{ module }}</h4>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div
                  v-for="permission in modulePermissions"
                  :key="permission.id"
                  class="flex flex-col md:flex-row md:items-center justify-between p-5 bg-white border border-gray-200 rounded-xl shadow-md hover:border-blue-500 transition-all duration-300 group relative border-r-4 border-r-blue-500 overflow-hidden"
                >
                  <div class="flex-1 ml-4 mb-4 md:mb-0 text-right">
                    <div class="text-sm text-gray-800 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100 font-medium whitespace-pre-wrap">
                      {{ permission.description || 'لا يوجد وصف متاح لهذه الصلاحية' }}
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 space-x-reverse border-t md:border-t-0 pt-3 md:pt-0 border-gray-100">
                    <button
                      @click="editPermission(permission)"
                      class="flex items-center text-white bg-blue-600 hover:bg-blue-700 px-4 ml-2 py-2 rounded-xl font-bold transition-all shadow-sm active:scale-95 text-sm"
                    >
                      تعديل
                    </button>
                    <button
                      @click="deletePermission(permission.id)"
                      class="flex items-center text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded-xl font-bold transition-all shadow-sm active:scale-95 text-sm"
                    >
                      حذف
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Permissions Modal -->
    <div
      v-if="showUserModal"
      class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
    >
      <div class="relative mx-auto p-8 border w-full max-w-lg shadow-2xl rounded-2xl bg-white animate-fade-in-up">
        <div class="mb-6 border-b border-gray-100 pb-4">
          <h3 class="text-2xl leading-6 font-bold text-gray-900">صلاحيات المستخدم</h3>
        </div>
        <div class="mt-2 px-1 py-3 text-right">
          <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100 text-gray-900 shadow-sm">
            <div class="flex justify-between mb-2">
              <span class="font-bold text-blue-800">المستخدم:</span>
              <span class="font-bold text-gray-900 text-lg">{{ selectedUser?.name }}</span>
            </div>
            <div class="flex justify-between mb-2">
              <span class="font-bold text-blue-800">الوظيفة:</span>
              <span class="font-bold text-gray-900">{{ selectedUser?.designation?.name || 'غير محدد' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-bold text-blue-800">النوع:</span>
              <span :class="['font-bold px-2 py-0.5 rounded text-sm', selectedUser?.is_admin ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">
                {{ selectedUser?.is_admin ? 'مدير نظام (كل الصلاحيات)' : 'مستخدم عادي' }}
              </span>
            </div>
          </div>
          <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
            <div v-if="selectedUser?.is_admin" class="text-green-600 font-extrabold text-center p-6 bg-green-50 rounded-xl border border-green-200">
              هذا المستخدم لديه كل الصلاحيات بالكامل
            </div>
            <div
              v-else-if="Object.keys(selectedUserPermissions).length === 0"
              class="text-gray-500 text-center p-6 bg-gray-50 rounded-xl border border-gray-100"
            >
              لا توجد صلاحيات مخصصة لهذا المستخدم
            </div>
            <div
              v-for="(description ,id) in selectedUserPermissions"
              :key="id"
              class="p-4 bg-white border border-gray-200 rounded-xl text-gray-900 font-bold flex items-center shadow-sm hover:border-blue-400 transition-all border-r-4 border-r-blue-500"
            >
              <div class="w-2 h-2 bg-blue-600 rounded-full ml-3 shadow-sm"></div>
              <span class="leading-relaxed">{{ description }}</span>
            </div>
          </div>
        </div>
        <div class="items-center px-4 py-3 mt-4 border-t border-gray-100 pt-6">
          <button
            @click="showUserModal = false"
            class="px-6 py-3 bg-gray-800 text-white text-lg font-bold rounded-xl w-full shadow-lg hover:bg-gray-900 transition-all active:scale-95"
          >
            إغلاق النافذة
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Permission Modal -->
    <div
      v-if="showCreatePermissionForm || editingPermissionId"
      class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
    >
      <div class="relative mx-auto p-8 border w-full max-w-lg shadow-2xl rounded-2xl bg-white animate-fade-in-up">
        <div class="mb-6 border-b border-gray-100 pb-4">
          <h3 class="text-2xl leading-6 font-bold text-gray-900 text-right">
            {{ editingPermissionId ? 'تعديل الصلاحية' : 'إضافة صلاحية جديدة' }}
          </h3>
          <p class="text-gray-500 mt-2 text-sm text-right">أدخل تفاصيل الصلاحية بدقة لضمان عمل النظام بشكل صحيح</p>
        </div>
        <form @submit.prevent="editingPermissionId ? updatePermission() : createPermission()" class="space-y-5 text-right">
          <!-- <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">اسم الصلاحية (بالعربية)</label>
            <input
              v-model="newPermission.name"
              type="text"
              required
              placeholder="مثال: إنشاء فرع جديد"
              class="block w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none bg-white text-gray-900 font-bold"
            />
          </div> -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">اسم الصلاحية</label>
            <textarea
              v-model="newPermission.description"
              rows="3"
              placeholder="وصف لوظيفة هذه الصلاحية..."
              class="block w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none resize-none bg-white text-gray-900 font-bold"
            ></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1">الوحدة (Module)</label>
              <input
                v-model="newPermission.module"
                type="text"
                required
                placeholder="مثال: branches"
                class="block w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none font-mono text-sm bg-white text-gray-900 font-bold"
              />
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1">الإجراء (Action)</label>
              <input
                v-model="newPermission.action"
                type="text"
                required
                placeholder="مثال: create"
                class="block w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none font-mono text-sm bg-white text-gray-900 font-bold"
              />
            </div>
          </div>
          <div class="flex space-x-4 space-x-reverse pt-4">
            <button
              type="submit"
              class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-xl font-extrabold hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all active:scale-95 text-lg"
            >
              {{ editingPermissionId ? 'تحديث البيانات' : 'حفظ الصلاحية' }}
            </button>
            <button
              type="button"
              @click="closePermissionForm"
              class="flex-1 bg-gray-100 text-gray-800 px-6 py-3 rounded-xl font-extrabold hover:bg-gray-200 transition-all active:scale-95 text-lg"
            >
              إلغاء
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { usePermissionService } from '@/composables/usePermissionService'

const router = useRouter()
const permissionService = usePermissionService()

const activeTab = ref('overview')
const designations = ref([])
const users = ref([])
const permissions = ref({})
const showUserModal = ref(false)
const selectedUser = ref(null)
const selectedUserPermissions = ref({})
const showCreatePermissionForm = ref(false)
const editingPermissionId = ref(null)
const newPermission = ref({
  name: '',
  description: '',
  module: '',
  action: ''
})

const tabs = [
  { key: 'overview', label: 'نظرة عامة' },
  { key: 'designations', label: 'صلاحيات الوظائف' },
  { key: 'users', label: 'صلاحيات المستخدمين' },
  { key: 'manage', label: 'إدارة الصلاحيات' }
]

const loadData = async () => {
  try {
    const [designationsRes, usersRes, permissionsRes] = await Promise.all([
      permissionService.getDesignationsWithPermissions(),
      permissionService.getUsersWithPermissions(),
      permissionService.getAllPermissions()
    ])
    
    designations.value = designationsRes.data
    users.value = usersRes.data
    permissions.value = permissionsRes.data
  } catch (error) {
    console.error('Error loading data:', error)
  }
}

const editDesignationPermissions = (designation) => {
  router.push(`/admin/permissions/designations/${designation.id}/edit`)
}

const showUserPermissions = async (user) => {
  try {
    const response = await permissionService.getUserPermissions(user.id)
    selectedUser.value = response.data.user
    selectedUserPermissions.value = response.data.permissions
    showUserModal.value = true
  } catch (error) {
    console.error('Error loading user permissions:', error)
  }
}

const createPermission = async () => {
  try {
    await permissionService.createPermission(newPermission.value)
    showCreatePermissionForm.value = false
    newPermission.value = { name: '', description: '', module: '', action: '' }
    await loadData()
  } catch (error) {
    console.error('Error creating permission:', error)
  }
}

const editPermission = (permission) => {
  editingPermissionId.value = permission.id
  newPermission.value = {
    name: permission.name,
    description: permission.description,
    module: permission.module,
    action: permission.action
  }
}

const closePermissionForm = () => {
  showCreatePermissionForm.value = false
  editingPermissionId.value = null
  newPermission.value = { name: '', description: '', module: '', action: '' }
}

const updatePermission = async () => {
  try {
    await permissionService.updatePermission(editingPermissionId.value, newPermission.value)
    closePermissionForm()
    await loadData()
  } catch (error) {
    console.error('Error updating permission:', error)
  }
}

const deletePermission = async (permissionId) => {
  if (confirm('هل أنت متأكد من حذف هذه الصلاحية؟')) {
    try {
      await permissionService.deletePermission(permissionId)
      await loadData()
    } catch (error) {
      console.error('Error deleting permission:', error)
    }
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
