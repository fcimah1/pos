import { ref } from 'vue';
import Swal from 'sweetalert2';

export function useApi() {
    const API_BASE = '/api';
    const isLoading = ref(false);

    const apiCall = async (endpoint, options = {}) => {
        const url = `${API_BASE}${endpoint}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const authToken = localStorage.getItem('token');

        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
                ...(authToken && { 'Authorization': `Bearer ${authToken}` }),
                ...options.headers,
            },
            ...options,
        };

        if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
            defaultOptions.body = JSON.stringify(options.body);
        }

        isLoading.value = true;
        try {
            const response = await fetch(url, defaultOptions);
            
            // قراءة الاستجابة كنص أولاً لتجنب خطأ JSON input
            const text = await response.text();
            let data = null;
            if (text) {
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }
            }

            if (!response.ok) {
                if (response.status === 401) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                    return;
                }

                const errorMessage = data?.message || `Error: ${response.status}`;
                
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: errorMessage,
                    confirmButtonText: 'حسناً',
                    background: '#1f2937', // لون داكن ليتناسب مع التصميم
                    color: '#fff',
                    customClass: {
                        popup: 'rounded-2xl border border-gray-700 shadow-2xl'
                    }
                });

                throw new Error(errorMessage);
            }

            return data;
        } catch (error) {
            if (error.message !== 'Unauthorized') {
                 console.error('API Error:', error);
            }
            throw error;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        apiCall,
        isLoading,
        Swal // تصدير Swal للاستخدام في المكونات الأخرى
    };
}
