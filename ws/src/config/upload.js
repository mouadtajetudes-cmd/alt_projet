const multer = require('multer')
const path = require('path')
const fs = require('fs')

const uploadsDir = path.join(__dirname, '../../uploads')
if (!fs.existsSync(uploadsDir)) {
  fs.mkdirSync(uploadsDir, { recursive: true })
}

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, uploadsDir)
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9)
    cb(null, uniqueSuffix + path.extname(file.originalname))
  }
})

const upload = multer({
  storage: storage,
  limits: { fileSize: 10 * 1024 * 1024 },
  fileFilter: (req, file, cb) => {
    const extension = path.extname(file.originalname).toLowerCase()
    const allowedImageExtensions = new Set([
      '.jpg',
      '.jpeg',
      '.png',
      '.gif',
      '.webp',
      '.bmp',
      '.svg',
      '.avif',
      '.jfif',
      '.heic',
      '.heif'
    ])
    const allowedDocumentExtensions = new Set([
      '.pdf',
      '.doc',
      '.docx',
      '.txt',
      '.zip',
      '.rar'
    ])
    const isImage = file.mimetype.startsWith('image/') && allowedImageExtensions.has(extension)
    const allowedDocumentMimeTypes = new Set([
      'application/pdf',
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'text/plain',
      'application/zip',
      'application/x-zip-compressed',
      'application/vnd.rar',
      'application/x-rar-compressed'
    ])
    const isDocument = allowedDocumentExtensions.has(extension) && allowedDocumentMimeTypes.has(file.mimetype)
    if (isImage || isDocument) return cb(null, true)
    cb(new Error('Type de fichier non autorisé!'))
  }
})

module.exports = { upload, uploadsDir }
