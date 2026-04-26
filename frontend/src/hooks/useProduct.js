import { useCallback, useEffect, useState } from 'react'
import useFetch from './useFetch'

const defaultForm = {
  name: '',
  description: '',
  price: '',
  stock: '',
}

const unauthorizedMessage = 'Silakan login dulu untuk mengelola produk.'

export default function useProducts({ isAuthenticated, getAuthHeaders }) {
  const { data: products, execute, loading } = useFetch([])
  const [form, setForm] = useState(defaultForm)
  const [editingId, setEditingId] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [productError, setProductError] = useState('')

  const loadProducts = useCallback(async () => {
    setProductError('')

    try {
      await execute('/api/products', {
        errorMessage: 'Gagal mengambil data produk.',
      })
    } catch (err) {
      setProductError(err.message)
    }
  }, [execute])

  useEffect(() => {
    const timerId = setTimeout(() => {
      loadProducts()
    }, 0)

    return () => clearTimeout(timerId)
  }, [loadProducts])

  const handleProductChange = useCallback((event) => {
    const { name, value } = event.target
    setForm((prev) => ({ ...prev, [name]: value }))
  }, [])

  const resetForm = useCallback(() => {
    setForm(defaultForm)
    setEditingId(null)
  }, [])

  const startEdit = useCallback((product) => {
    setEditingId(product.id)
    setForm({
      name: product.name,
      description: product.description ?? '',
      price: String(product.price),
      stock: String(product.stock),
    })
  }, [])

  const handleProductSubmit = useCallback(
    async (event) => {
      event.preventDefault()
      setSubmitting(true)
      setProductError('')

      if (!isAuthenticated) {
        setSubmitting(false)
        setProductError(unauthorizedMessage)
        return
      }

      const payload = {
        name: form.name,
        description: form.description || null,
        price: Number(form.price),
        stock: Number(form.stock),
      }

      try {
        const url = editingId ? `/api/products/${editingId}` : '/api/products'
        const method = editingId ? 'PUT' : 'POST'

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...getAuthHeaders(),
          },
          body: JSON.stringify(payload),
        })

        if (!response.ok) {
          throw new Error('Validasi gagal. Periksa input Anda.')
        }

        await loadProducts()
        resetForm()
      } catch (err) {
        setProductError(err.message)
      } finally {
        setSubmitting(false)
      }
    },
    [editingId, form.description, form.name, form.price, form.stock, getAuthHeaders, isAuthenticated, loadProducts, resetForm],
  )

  const handleDelete = useCallback(
    async (id) => {
      if (!window.confirm('Hapus produk ini?')) {
        return
      }

      if (!isAuthenticated) {
        setProductError(unauthorizedMessage)
        return
      }

      setProductError('')

      try {
        const response = await fetch(`/api/products/${id}`, {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
            ...getAuthHeaders(),
          },
        })

        if (!response.ok) {
          throw new Error('Gagal menghapus produk.')
        }

        await loadProducts()
      } catch (err) {
        setProductError(err.message)
      }
    },
    [getAuthHeaders, isAuthenticated, loadProducts],
  )

  return {
    editingId,
    form,
    loading,
    productError,
    products,
    submitting,
    handleDelete,
    handleProductChange,
    handleProductSubmit,
    loadProducts,
    resetForm,
    startEdit,
  }
}