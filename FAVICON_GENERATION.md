# Eurofactu Favicon Generation Guide

Este documento contiene las instrucciones para generar todos los archivos favicon necesarios para Eurofactu.

## ✅ Estado Actual

Todos los favicons han sido generados exitosamente:

- ✅ `public/favicon.svg` - Favicon moderno en SVG (soporta modo oscuro/claro automáticamente)
- ✅ `public/favicon.ico` - Favicon ICO para navegadores antiguos (32x32 + 16x16)
- ✅ `public/apple-touch-icon.png` - 180x180 para iOS home screen
- ✅ `public/icon-192.png` - 192x192 para Android/PWA
- ✅ `public/icon-512.png` - 512x512 para Android/PWA
- ✅ `public/site.webmanifest` - Manifest para Progressive Web App
- ✅ `resources/views/partials/head.blade.php` - Referencias actualizadas

## 🚀 Regenerar Favicons

Si necesitas regenerar los favicons (por ejemplo, después de cambiar el logo), simplemente ejecuta:

```bash
npm run favicons
```

## 🎨 Modificar el Logo

1. Edita el archivo `resources/views/components/app-logo-icon.blade.php`
2. Ejecuta `npm run favicons` para regenerar todos los archivos
3. Recarga el navegador con Ctrl+F5 para ver los cambios

## 📱 Archivos Generados

### favicon.svg (Moderno)
- Formato vectorial escalable
- Soporta modo oscuro y claro automáticamente
- Compatible con navegadores modernos (Chrome, Firefox, Safari, Edge)

### favicon.ico (Compatibilidad)
- Contiene 2 tamaños: 32x32 y 16x16
- Para navegadores antiguos y compatibilidad total

### apple-touch-icon.png
- 180x180 píxeles
- Se usa cuando el usuario añade el sitio a la home screen en iOS
- Con esquinas redondeadas aplicadas automáticamente por iOS

### icon-192.png y icon-512.png
- Para Android y Progressive Web Apps
- Se referencian en `site.webmanifest`

### site.webmanifest
- Manifest para PWA
- Define el nombre de la app, colores de tema, iconos, etc.

## 🔧 Scripts Disponibles

- `generate-all-favicons.js` - Script automatizado principal (recomendado)
- `generate-favicons-auto.js` - Genera PNG sin ICO
- `generate-favicons.js` - Solo muestra instrucciones

## 📝 Notas Técnicas

- El SVG usa `currentColor` y media queries para adaptarse al modo oscuro/claro
- Los PNG se generan usando `sharp` (procesamiento de imágenes en Node.js)
- El ICO se genera usando `to-ico` (compatible con múltiples tamaños)
- El logo base está en `resources/views/components/app-logo-icon.blade.php`

## 🌐 Referencias en HTML

Las referencias se incluyen automáticamente en todas las páginas a través de `resources/views/partials/head.blade.php`:

```html
<!-- Favicons -->
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#1b1b18">
```

## 🎯 Compatibilidad

- ✅ Chrome/Edge/Opera - Usa favicon.svg
- ✅ Firefox - Usa favicon.svg
- ✅ Safari - Usa favicon.svg o favicon.ico
- ✅ iOS Safari - Usa apple-touch-icon.png
- ✅ Android Chrome - Usa icon-192.png y icon-512.png (desde manifest)
- ✅ Internet Explorer - Usa favicon.ico

## 💡 Consejos

- Los navegadores cachean los favicons agresivamente. Usa Ctrl+F5 para forzar recarga.
- En desarrollo, puedes necesitar limpiar la caché del navegador completamente.
- El favicon.svg cambia automáticamente de color según el modo oscuro/claro del sistema.
