import { useCallback, useState } from 'react'

export default function useFetch(initialData = null) {
  const [data, setData] = useState(initialData)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')

  const execute = useCallback(async (url, options = {}) => {
    const {
      errorMessage = 'Terjadi kesalahan saat mengambil data.',
      parseAs = 'json',
      ...fetchOptions
    } = options

    setLoading(true)
    setError('')

    try {
      const response = await fetch(url, fetchOptions)

      if (!response.ok) {
        throw new Error(errorMessage)
      }

      let result = null

      if (parseAs === 'json') {
        result = await response.json()
      } else if (parseAs === 'text') {
        result = await response.text()
      }

      setData(result)
      return result
    } catch (err) {
      setError(err.message)
      throw err
    } finally {
      setLoading(false)
    }
  }, [])

  return {
    data,
    error,
    execute,
    loading,
    setData,
    setError,
  }
}