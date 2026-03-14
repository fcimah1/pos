import { ref } from 'vue'
import { useApi } from './useApi'

export function usePermissionService() {
  const { apiCall } = useApi()

  const getDesignationsWithPermissions = async () => {
    const response = await apiCall('/designations', {
      params: { with: 'permissions' }
    })
    return response
  }

  const getUsersWithPermissions = async () => {
    const response = await apiCall('/users', {
      params: { 
        with: 'designation,designation.permissions',
        append: 'is_admin,designation.permissions_count'
      }
    })
    return response
  }

  const getAllPermissions = async () => {
    const response = await apiCall('/permissions')
    return response
  }

  const getDesignationWithPermissions = async (id) => {
    const response = await apiCall(`/designations/${id}`, {
      params: { with: 'permissions' }
    })
    return response
  }

  const getUserPermissions = async (userId) => {
    const response = await apiCall(`/permissions/users/${userId}`)
    return response
  }

  const createPermission = async (permission) => {
    const response = await apiCall('/permissions', {
      method: 'POST',
      body: permission
    })
    return response
  }

  const updatePermission = async (id, permission) => {
    const response = await apiCall(`/permissions/${id}`, {
      method: 'PUT',
      body: permission
    })
    return response
  }

  const deletePermission = async (id) => {
    const response = await apiCall(`/permissions/${id}`, {
      method: 'DELETE'
    })
    return response
  }

  const updateDesignationPermissions = async (designationId, data) => {
    const response = await apiCall(`/permissions/designations/${designationId}/permissions`, {
      method: 'PUT',
      body: data
    })
    return response
  }

  return {
    getDesignationsWithPermissions,
    getUsersWithPermissions,
    getAllPermissions,
    getDesignationWithPermissions,
    getUserPermissions,
    createPermission,
    updatePermission,
    deletePermission,
    updateDesignationPermissions
  }
}
