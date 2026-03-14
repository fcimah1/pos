<template>
  <div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
          تعديل صلاحيات وظيفة: {{ designation.name }}
        </h1>
        <button
          @click="$router.go(-1)"
          class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
        >
          رجوع
        </button>
      </div>

      <form @submit.prevent="updatePermissions">
        <div
          v-for="(modulePermissions, module) in permissions"
          :key="module"
          class="mb-6"
        >
          <h3 class="text-lg font-semibold mb-3 text-gray-700">{{ module }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <label
              v-for="permission in modulePermissions"
              :key="permission.id"
              class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-400 transition-all duration-200 bg-white shadow-md group border-r-4 border-r-blue-500"
            >
              <input
                type="checkbox"
                :value="permission.id"
                v-model="selectedPermissions"
                class="ml-3 h-6 w-6 text-blue-600 focus:ring-blue-500 border-gray-400 rounded-md transition-all duration-200 cursor-pointer"
              />
              <div class="flex flex-col mr-2">
                <div class="font-extrabold text-gray-900 group-hover:text-blue-700 transition-colors text-lg">{{ permission.name }}</div>
                <div class="text-xs font-mono text-blue-600 mb-1">{{ permission.module }}.{{ permission.action }}</div>
                <div class="text-sm text-gray-800 font-medium leading-relaxed bg-gray-50 p-1.5 rounded border border-gray-100">{{ permission.description || 'لا يوجد وصف متاح' }}</div>
              </div>
            </label>
          </div>
        </div>

        <div class="flex justify-end space-x-4 space-x-reverse">
          <button
            type="button"
            @click="selectAllPermissions"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
          >
            تحديد الكل
          </button>
          <button
            type="button"
            @click="deselectAllPermissions"
            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
          >
            إلغاء تحديد الكل
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
          >
            {{ loading ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success/Error Message -->
  <div
    v-if="message.show"
    :class="[
      'fixed top-4 right-4 z-50',
      message.type === 'success' ? 'text-green-800' : 'text-red-800'
    ]"
  >
    <div class="bg-white rounded-lg shadow-lg p-4 max-w-sm">
      <div class="flex items-center">
        <div class="ml-3">
          <svg
            v-if="message.type === 'success'"
            class="h-6 w-6 text-green-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <svg
            v-else
            class="h-6 w-6 text-red-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>
        <div class="text-sm font-medium">{{ message.text }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePermissionService } from '@/composables/usePermissionService'

const route = useRoute()
const router = useRouter()
const permissionService = usePermissionService()

const designation = ref({})
const permissions = ref({})
const selectedPermissions = ref([])
const loading = ref(false)
const message = ref({
  show: false,
  text: '',
  type: 'success'
})

const loadData = async () => {
  try {
    const [designationRes, permissionsRes] = await Promise.all([
      permissionService.getDesignationWithPermissions(route.params.id),
      permissionService.getAllPermissions()
    ])
    
    designation.value = designationRes.data
    permissions.value = permissionsRes.data
    
    // تحديد الصلاحيات الحالية
    selectedPermissions.value = designation.value.permissions?.map(p => p.id) || []
  } catch (error) {
    console.error('Error loading data:', error)
    showMessage('حدث خطأ في تحميل البيانات', 'error')
  }
}

const selectAllPermissions = () => {
  const allPermissionIds = []
  Object.values(permissions.value).forEach(modulePermissions => {
    modulePermissions.forEach(permission => {
      allPermissionIds.push(permission.id)
    })
  })
  selectedPermissions.value = allPermissionIds
}

const deselectAllPermissions = () => {
  selectedPermissions.value = []
}

const showMessage = (text, type = 'success') => {
  message.value = { show: true, text, type }
  setTimeout(() => {
    message.value.show = false
  }, 3000)
}

const updatePermissions = async () => {
  loading.value = true
  
  try {
    await permissionService.updateDesignationPermissions(route.params.id, {
      permissions: selectedPermissions.value
    })
    
    showMessage('تم تحديث الصلاحيات بنجاح', 'success')
    
    // إعادة التوجيه بعد ثانية واحدة
    setTimeout(() => {
      router.push('/admin/permissions')
    }, 1000)
  } catch (error) {
    console.error('Error updating permissions:', error)
    showMessage(error.response?.data?.message || 'حدث خطأ ما', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
