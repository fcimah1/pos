<template>
    <div class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold text-white text-center mb-6">تسجيل الدخول - POS</h1>
            
            <form @submit.prevent="login" class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-1">البريد الإلكتروني</label>
                    <input 
                        v-model="email" 
                        type="email" 
                        required
                        class="w-full bg-gray-700 text-white rounded px-4 py-2 border border-gray-600 focus:border-blue-500 focus:outline-none"
                        placeholder="admin@example.com"
                    >
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-1">كلمة المرور</label>
                    <input 
                        v-model="password" 
                        type="password" 
                        required
                        class="w-full bg-gray-700 text-white rounded px-4 py-2 border border-gray-600 focus:border-blue-500 focus:outline-none"
                        placeholder="password"
                    >
                </div>
                
                <div v-if="error" class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-2 rounded text-sm">
                    {{ error }}
                </div>
                
                <button 
                    type="submit" 
                    :disabled="isLoading"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition disabled:opacity-50"
                >
                    <span v-if="isLoading">جاري تسجيل الدخول...</span>
                    <span v-else>تسجيل الدخول</span>
                </button>
            </form>
            
            <div class="mt-4 text-center text-gray-400 text-sm">
                <p>للاختبار:</p>
                <p>Email: admin@example.com</p>
                <p>Password: password</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const error = ref('');
const isLoading = ref(false);
const router = useRouter();

const login = async () => {
    isLoading.value = true;
    error.value = '';
    
    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value
            })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'فشل تسجيل الدخول');
        }
        
        // حفظ التوكن في localStorage
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
        
        // الانتقال للـ POS
        router.push('/pos');
        
    } catch (err) {
        error.value = err.message || 'حدث خطأ أثناء تسجيل الدخول';
    } finally {
        isLoading.value = false;
    }
};
</script>
