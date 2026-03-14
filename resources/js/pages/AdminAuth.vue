<template>
  <div class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
      <div>
        <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-600">
          <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          مصادقة المدير
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          هذه الصفحة تتطلب صلاحيات المدير. أدخل كلمة مرور مدير النظام للمتابعة.
        </p>
        @if(session('message'))
          <div class="mt-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
            {{ session('message') }}
          </div>
        @endif
      </div>

      <form @submit.prevent="authenticate" class="mt-8 space-y-6">
        <div>
          <label for="admin_password" class="block text-sm font-medium text-gray-700">
            كلمة مرور المدير
          </label>
          <div class="mt-1">
            <input
              id="admin_password"
              v-model="form.admin_password"
              type="password"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="أدخل كلمة مرور المدير"
              autocomplete="current-password"
            >
          </div>
          <div v-if="errors.admin_password" class="mt-2 text-sm text-red-600">
            {{ errors.admin_password }}
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg v-if="!loading" class="h-5 w-5 text-blue-500 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <div v-else class="h-5 w-5 text-blue-500 animate-spin rounded-full border-2 border-solid border-current border-r-transparent"></div>
            </span>
            {{ loading ? 'جاري المصادقة...' : 'مصادقة والوصول' }}
          </button>
        </div>

        <div class="text-center">
          <a href="/login" class="text-sm text-blue-600 hover:text-blue-500">
            العودة لتسجيل الدخول العادي
          </a>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'

const { apiCall } = useApi()

const form = ref({
  admin_password: ''
})

const errors = ref({})
const loading = ref(false)

const authenticate = async () => {
  loading.value = true
  errors.value = {}
  
  try {
    // إنشاء form data للـ POST request
    const formData = new FormData()
    formData.append('admin_password', form.value.admin_password)
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content)

    const response = await fetch('/admin/auth', {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    const data = await response.json()

    if (response.ok) {
      // تم المصادقة بنجاح، توجيه للصفحة المطلوبة
      window.location.href = data.redirect || '/admin'
    } else {
      // عرض رسائل الخطأ
      if (data.errors) {
        errors.value = data.errors
      } else {
        errors.value.admin_password = data.message || 'حدث خطأ في المصادقة'
      }
    }
  } catch (error) {
    console.error('Authentication error:', error)
    errors.value.admin_password = 'حدث خطأ في الاتصال'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  // التركيز تلقائي على حقل كلمة المرور
  document.getElementById('admin_password')?.focus()
})
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
