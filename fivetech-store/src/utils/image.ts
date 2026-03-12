const STORAGE_BASE = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'

/**
 * Chuyển đổi đường dẫn ảnh từ DB thành URL đầy đủ.
 * Nếu path đã là URL đầy đủ (bắt đầu bằng http) thì giữ nguyên.
 */
export const storageUrl = (path: string | null | undefined): string => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `${STORAGE_BASE}/${path.replace(/^\/+/, '')}`
}
