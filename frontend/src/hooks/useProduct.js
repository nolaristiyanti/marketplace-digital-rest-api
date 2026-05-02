import { useCallback, useEffect, useState } from 'react'
import useFetch from './useFetch'

const defaultForm = {
  title: '',
  description: '',
  price: '',
  category_id: '',
  rating: '0',
  file_path: '',
  stock: '0',
}

const unauthorizedMessage = 'Silakan login dulu untuk mengelola produk.'

export default function useProducts({ isAuthenticated, getAuthHeaders }) {
  const { data: products, execute, loading, setData } = useFetch([])
  const [form, setForm] = useState(defaultForm)
  const [editingId, setEditingId] = useState(null)
  const [submitting, setSubmitting] = useState(false)
  const [productError, setProductError] = useState('')

  const BASE_URL = import.meta.env.VITE_API_URL;

  const loadProducts = useCallback(async () => {
    setProductError('')

    try {
      const result = await execute(`${BASE_URL}/products`, {
        errorMessage: 'Gagal mengambil data produk.',
      })
      setData(Array.isArray(result?.data) ? result.data : [])
    } catch (err) {
      setProductError(err.message)
    }
  }, [execute, setData])

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
      title: product.title ?? '',
      description: product.description ?? '',
      price: String(product.price),
      category_id: String(product.category?.id ?? ''),
      rating: String(product.rating ?? 0),
      file_path: product.file_path ?? '',
      stock: String(product.stock ?? 0),
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
        title: form.title,
        description: form.description,
        price: Number(form.price),
        category_id: Number(form.category_id),
        rating: Number(form.rating),
        file_path: form.file_path,
        stock: Number(form.stock),
      }

      try {
        const url = editingId ? `${BASE_URL}/products/${editingId}` : `${BASE_URL}/products`
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
    [editingId, form.category_id, form.description, form.file_path, form.price, form.rating, form.stock, form.title, getAuthHeaders, isAuthenticated, loadProducts, resetForm],
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
        const response = await fetch(`${BASE_URL}/products/${id}`, {
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